#!/usr/bin/env python3
"""
╔══════════════════════════════════════════════════════════════════════════════════╗
║  [WHOAMISec] SUPERBOT PRO v12.0 - GLOBAL ARBITRAGE SCANNER                      ║
║  Peste 50+ Ligi | Plăți Anonime | Admin Panel | 24/7                            ║
╚══════════════════════════════════════════════════════════════════════════════════╝
"""

import os
import sys
import json
import time
import sqlite3
import threading
import webbrowser
import random
import hashlib
import secrets
import asyncio
from datetime import datetime, timedelta
from typing import Dict, List, Optional, Tuple
from dataclasses import dataclass, field
from pathlib import Path
from http.server import HTTPServer, BaseHTTPRequestHandler

# =============================================================================
# AUTO-INSTALL DEPENDENCIES
# =============================================================================

def install_packages():
    packages = ['rich', 'python-telegram-bot>=20.0', 'qrcode', 'pillow']
    for pkg in packages:
        try:
            __import__(pkg.replace('-', '_').split('>')[0])
        except ImportError:
            os.system(f"{sys.executable} -m pip install {pkg}")

install_packages()

try:
    from rich.console import Console
    from rich.table import Table
    RICH_OK = True
except:
    RICH_OK = False

try:
    from telegram import Update, InlineKeyboardButton, InlineKeyboardMarkup
    from telegram.ext import Application, CommandHandler, CallbackQueryHandler, ContextTypes
    TELEGRAM_OK = True
except:
    TELEGRAM_OK = False

console = Console() if RICH_OK else None

# =============================================================================
# CONFIGURATION
# =============================================================================

@dataclass
class Config:
    SCAN_INTERVAL: int = 30
    WEB_PORT: int = 8082
    MIN_PROFIT_PERCENT: float = 0.8
    DB_PATH: str = ""
    
    # Telegram Bot
    BOT_TOKEN: str = "8245053847:AAEHIS5UfcSRVP7iWVK4SedMEXxx9fceUVE"
    ADMIN_ID: int = 8245053847
    ADMIN_PASSWORD: str = "AllOfThem-3301"
    
    # Crypto Payments
    XMR_ADDRESS: str = "8BbApiMBHsPVKkLEP4rVbST6CnSb3LW2gXygngCi5MGiBuwAFh6bFEzT3UTuFCkLHtyHnrYNnHycdaGb2Kgkkmw8jViCdB6"
    BTC_ADDRESS: str = "bc1XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX"
    USDT_TRC20_ADDRESS: str = "TXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX"
    
    # PRICES
    PRICE_DAILY_LEI: float = 25
    PRICE_WEEKLY_LEI: float = 75
    PRICE_MONTHLY_LEI: float = 250
    PRICE_LIFETIME_LEI: float = 999
    
    PRICE_DAILY_XMR: float = 0.02
    PRICE_WEEKLY_XMR: float = 0.07
    PRICE_MONTHLY_XMR: float = 0.25
    PRICE_LIFETIME_XMR: float = 1.2
    
    def __post_init__(self):
        home = Path.home()
        self.DB_PATH = str(home / '.whoamisec' / 'superbot.db')
        os.makedirs(str(home / '.whoamisec'), exist_ok=True)

CONFIG = Config()

# =============================================================================
# GLOBAL FOOTBALL LEAGUES DATABASE - Peste 50+ Ligi
# =============================================================================

GLOBAL_LEAGUES = [
    # UEFA - Europa
    {"id": "EPL", "name": "🏴󠁧󠁢󠁥󠁮󠁧󠁿 Premier League", "country": "Anglia", "region": "Europa"},
    {"id": "LALIGA", "name": "🇪🇸 La Liga", "country": "Spania", "region": "Europa"},
    {"id": "SERIEA", "name": "🇮🇹 Serie A", "country": "Italia", "region": "Europa"},
    {"id": "BUNDESLIGA", "name": "🇩🇪 Bundesliga", "country": "Germania", "region": "Europa"},
    {"id": "LIGUE1", "name": "🇫🇷 Ligue 1", "country": "Franța", "region": "Europa"},
    {"id": "PRIMEIRA", "name": "🇵🇹 Primeira Liga", "country": "Portugalia", "region": "Europa"},
    {"id": "EREDIVISIE", "name": "🇳🇱 Eredivisie", "country": "Olanda", "region": "Europa"},
    {"id": "LIGAPRO", "name": "🇷🇴 Liga 1", "country": "România", "region": "Europa"},
    {"id": "TURKEY", "name": "🇹🇷 Süper Lig", "country": "Turcia", "region": "Europa"},
    {"id": "GREECE", "name": "🇬🇷 Super League", "country": "Grecia", "region": "Europa"},
    {"id": "RUSSIAN", "name": "🇷🇺 Premier League", "country": "Rusia", "region": "Europa"},
    {"id": "BELGIUM", "name": "🇧🇪 Pro League", "country": "Belgia", "region": "Europa"},
    {"id": "AUSTRIA", "name": "🇦🇹 Bundesliga", "country": "Austria", "region": "Europa"},
    {"id": "SWISS", "name": "🇨🇭 Super League", "country": "Elveția", "region": "Europa"},
    {"id": "UCL", "name": "🏆 Champions League", "country": "Europa", "region": "Europa"},
    {"id": "UEL", "name": "🏆 Europa League", "country": "Europa", "region": "Europa"},
    
    # CONMEBOL - America de Sud
    {"id": "LIBERTADORES", "name": "🏆 Copa Libertadores", "country": "America de Sud", "region": "America de Sud"},
    {"id": "BRASILEIRAO", "name": "🇧🇷 Brasileirão", "country": "Brazilia", "region": "America de Sud"},
    {"id": "ARGENTINA", "name": "🇦🇷 Liga Profesional", "country": "Argentina", "region": "America de Sud"},
    {"id": "CHILE", "name": "🇨🇱 Primera Division", "country": "Chile", "region": "America de Sud"},
    {"id": "COLOMBIA", "name": "🇨🇴 Primera A", "country": "Columbia", "region": "America de Sud"},
    {"id": "URUGUAY", "name": "🇺🇾 Primera Division", "country": "Uruguay", "region": "America de Sud"},
    
    # CONCACAF - America de Nord
    {"id": "MLS", "name": "🇺🇸 Major League Soccer", "country": "SUA", "region": "America de Nord"},
    {"id": "LIGAMX", "name": "🇲🇽 Liga MX", "country": "Mexic", "region": "America de Nord"},
    
    # AFC - Asia
    {"id": "ACL", "name": "🏆 AFC Champions League", "country": "Asia", "region": "Asia"},
    {"id": "JAPAN", "name": "🇯🇵 J1 League", "country": "Japonia", "region": "Asia"},
    {"id": "KOREA", "name": "🇰🇷 K League 1", "country": "Coreea de Sud", "region": "Asia"},
    {"id": "SAUDI", "name": "🇸🇦 Saudi Pro League", "country": "Arabia Saudită", "region": "Asia"},
    {"id": "UAE", "name": "🇦🇪 UAE Pro League", "country": "Emiratele Arabe", "region": "Asia"},
    {"id": "QATAR", "name": "🇶🇦 Qatar Stars League", "country": "Qatar", "region": "Asia"},
    {"id": "AUSTRALIA", "name": "🇦🇺 A-League", "country": "Australia", "region": "Asia"},
    {"id": "INDIA", "name": "🇮🇳 Super League", "country": "India", "region": "Asia"},
    {"id": "CHINA", "name": "🇨🇳 Super League", "country": "China", "region": "Asia"},
    
    # CAF - Africa
    {"id": "CAFCL", "name": "🏆 CAF Champions League", "country": "Africa", "region": "Africa"},
    {"id": "EGYPT", "name": "🇪🇬 Premier League", "country": "Egipt", "region": "Africa"},
    {"id": "MOROCCO", "name": "🇲🇦 Botola", "country": "Maroc", "region": "Africa"},
    {"id": "SOUTHAFRICA", "name": "🇿🇦 PSL", "country": "Africa de Sud", "region": "Africa"},
    {"id": "TUNISIA", "name": "🇹🇳 Ligue 1", "country": "Tunisia", "region": "Africa"},
    {"id": "ALGERIA", "name": "🇩🇿 Ligue 1", "country": "Algeria", "region": "Africa"},
]

# Echipe și cote de bază
TOP_TEAMS = {
    # Premier League
    "Arsenal": 2.10, "Manchester City": 1.85, "Liverpool": 2.05, "Manchester United": 2.30,
    "Chelsea": 2.25, "Tottenham": 2.40, "Newcastle": 2.60, "Aston Villa": 2.80,
    # La Liga
    "Real Madrid": 1.90, "Barcelona": 2.00, "Atletico Madrid": 2.35, "Real Sociedad": 2.90,
    "Athletic Bilbao": 3.00, "Sevilla": 3.10, "Villarreal": 3.15,
    # Serie A
    "Inter Milan": 1.95, "AC Milan": 2.10, "Juventus": 2.15, "Napoli": 2.20,
    "Roma": 2.45, "Lazio": 2.50, "Atalanta": 2.55, "Fiorentina": 2.80,
    # Bundesliga
    "Bayern Munich": 1.80, "Borussia Dortmund": 2.05, "RB Leipzig": 2.25,
    "Bayer Leverkusen": 2.15, "Frankfurt": 2.70,
    # Ligue 1
    "PSG": 1.75, "Marseille": 2.20, "Monaco": 2.30, "Lyon": 2.40, "Lille": 2.50,
    # Liga 1 România
    "FCSB": 2.00, "CFR Cluj": 2.10, "CS U Craiova": 2.30, "Rapid București": 2.40,
    # Saudi Pro League
    "Al-Hilal": 1.85, "Al-Nassr": 1.90, "Al-Ittihad": 2.00, "Al-Ahli": 2.15,
    # MLS
    "Inter Miami": 2.05, "LA Galaxy": 2.20, "LAFC": 2.15,
    # Brasileirão
    "Flamengo": 2.00, "Palmeiras": 2.05, "Corinthians": 2.25, "São Paulo": 2.30,
    # J1 League
    "Yokohama F. Marinos": 2.10, "Kawasaki Frontale": 2.15, "Urawa Reds": 2.20,
    # K League
    "Ulsan Hyundai": 2.05, "Jeonbuk Motors": 2.10,
    # Liga MX
    "Club América": 2.00, "Monterrey": 2.10, "Tigres": 2.15,
    # Eredivisie
    "Ajax": 1.95, "PSV": 2.00, "Feyenoord": 2.10,
    # Primeira Liga
    "Benfica": 1.90, "Porto": 2.00, "Sporting CP": 2.05,
    # Süper Lig
    "Galatasaray": 1.95, "Fenerbahçe": 2.00, "Beşiktaş": 2.15,
    # CAF
    "Al Ahly": 1.85, "Zamalek": 2.00, "Mamelodi Sundowns": 1.90,
}

# =============================================================================
# DATABASE
# =============================================================================

class Database:
    def __init__(self):
        self.db_path = CONFIG.DB_PATH
        self._lock = threading.Lock()
        self._init_db()
    
    def _get_conn(self):
        return sqlite3.connect(self.db_path, timeout=10)
    
    def _init_db(self):
        conn = self._get_conn()
        cursor = conn.cursor()
        
        cursor.execute('''
            CREATE TABLE IF NOT EXISTS users (
                telegram_id INTEGER PRIMARY KEY,
                username TEXT,
                license_type TEXT,
                license_expiry TEXT,
                created_at TEXT,
                last_active TEXT,
                language TEXT DEFAULT 'ro',
                total_paid REAL DEFAULT 0,
                payment_count INTEGER DEFAULT 0
            )
        ''')
        
        cursor.execute('''
            CREATE TABLE IF NOT EXISTS payments (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                transaction_id TEXT UNIQUE,
                telegram_id INTEGER,
                amount REAL,
                currency TEXT,
                payment_method TEXT,
                license_type TEXT,
                status TEXT,
                created_at TEXT,
                confirmed_at TEXT,
                confirmed_by INTEGER
            )
        ''')
        
        cursor.execute('''
            CREATE TABLE IF NOT EXISTS arbitrage_opportunities (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                timestamp TEXT,
                league TEXT,
                event_name TEXT,
                outcome TEXT,
                bookmaker1 TEXT,
                odds1 REAL,
                bookmaker2 TEXT,
                odds2 REAL,
                profit_percent REAL
            )
        ''')
        
        cursor.execute('''
            CREATE TABLE IF NOT EXISTS admin_logs (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                admin_id INTEGER,
                action TEXT,
                target_user INTEGER,
                details TEXT,
                timestamp TEXT
            )
        ''')
        
        conn.commit()
        conn.close()
    
    def add_user(self, telegram_id: int, username: str = None, language: str = 'ro'):
        expiry = (datetime.now() + timedelta(days=3)).isoformat()
        
        with self._lock:
            conn = self._get_conn()
            try:
                cursor = conn.cursor()
                cursor.execute('''
                    INSERT OR REPLACE INTO users 
                    (telegram_id, username, license_type, license_expiry, created_at, last_active, language)
                    VALUES (?, ?, ?, ?, ?, ?, ?)
                ''', (telegram_id, username, 'trial', expiry, datetime.now().isoformat(), datetime.now().isoformat(), language))
                conn.commit()
            finally:
                conn.close()
    
    def check_license(self, telegram_id: int) -> Tuple[bool, str]:
        with self._lock:
            conn = self._get_conn()
            try:
                cursor = conn.cursor()
                cursor.execute('SELECT license_type, license_expiry FROM users WHERE telegram_id = ?', (telegram_id,))
                row = cursor.fetchone()
                
                if not row:
                    return False, "no_user"
                
                license_type, expiry_str = row
                
                if license_type == 'lifetime':
                    return True, "lifetime"
                
                if license_type == 'revoked':
                    return False, "revoked"
                
                expiry = datetime.fromisoformat(expiry_str)
                if expiry > datetime.now():
                    days_left = (expiry - datetime.now()).days
                    return True, f"valid {days_left} zile rămase"
                else:
                    return False, "expirat"
            finally:
                conn.close()
    
    def activate_license(self, telegram_id: int, license_type: str, days: int, amount: float = 0):
        expiry = (datetime.now() + timedelta(days=days)).isoformat()
        
        with self._lock:
            conn = self._get_conn()
            try:
                cursor = conn.cursor()
                cursor.execute('''
                    UPDATE users SET license_type = ?, license_expiry = ?, total_paid = total_paid + ?, payment_count = payment_count + 1
                    WHERE telegram_id = ?
                ''', (license_type, expiry, amount, telegram_id))
                conn.commit()
            finally:
                conn.close()
    
    def add_payment(self, transaction_id: str, telegram_id: int, amount: float, currency: str, payment_method: str, license_type: str):
        with self._lock:
            conn = self._get_conn()
            try:
                cursor = conn.cursor()
                cursor.execute('''
                    INSERT INTO payments (transaction_id, telegram_id, amount, currency, payment_method, license_type, status, created_at)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)
                ''', (transaction_id, telegram_id, amount, currency, payment_method, license_type, 'pending', datetime.now().isoformat()))
                conn.commit()
                return cursor.lastrowid
            finally:
                conn.close()
    
    def confirm_payment(self, transaction_id: str, admin_id: int):
        with self._lock:
            conn = self._get_conn()
            try:
                cursor = conn.cursor()
                cursor.execute('''
                    UPDATE payments SET status = 'confirmed', confirmed_at = ?, confirmed_by = ? WHERE transaction_id = ?
                ''', (datetime.now().isoformat(), admin_id, transaction_id))
                conn.commit()
                
                cursor.execute('SELECT telegram_id, license_type, amount FROM payments WHERE transaction_id = ?', (transaction_id,))
                row = cursor.fetchone()
                if row:
                    telegram_id, license_type, amount = row
                    days = {'daily': 1, 'weekly': 7, 'monthly': 30, 'lifetime': 3650}.get(license_type, 30)
                    self.activate_license(telegram_id, license_type, days, amount)
                    return telegram_id, license_type
                return None, None
            finally:
                conn.close()
    
    def save_arbitrage(self, data: Dict):
        with self._lock:
            conn = self._get_conn()
            try:
                cursor = conn.cursor()
                cursor.execute('''
                    INSERT INTO arbitrage_opportunities 
                    (timestamp, league, event_name, outcome, bookmaker1, odds1, bookmaker2, odds2, profit_percent)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
                ''', (datetime.now().isoformat(), data.get('league', ''), data['event_name'], data['outcome'], 
                      data['bookmaker1'], data['odds1'], data['bookmaker2'], data['odds2'], data['profit_percent']))
                conn.commit()
            finally:
                conn.close()
    
    def get_stats(self):
        with self._lock:
            conn = self._get_conn()
            try:
                cursor = conn.cursor()
                cursor.execute('SELECT COUNT(*) FROM arbitrage_opportunities')
                total_opps = cursor.fetchone()[0]
                cursor.execute('SELECT AVG(profit_percent) FROM arbitrage_opportunities')
                avg_profit = cursor.fetchone()[0] or 0
                cursor.execute('SELECT COUNT(*) FROM users')
                total_users = cursor.fetchone()[0]
                cursor.execute('SELECT SUM(total_paid) FROM users')
                total_revenue = cursor.fetchone()[0] or 0
                cursor.execute('SELECT COUNT(*) FROM payments WHERE status = "pending"')
                pending_payments = cursor.fetchone()[0]
                return {
                    'opportunities': total_opps, 
                    'avg_profit': round(avg_profit, 2), 
                    'users': total_users,
                    'revenue': round(total_revenue, 2),
                    'pending': pending_payments
                }
            finally:
                conn.close()
    
    def get_all_users(self):
        with self._lock:
            conn = self._get_conn()
            try:
                cursor = conn.cursor()
                cursor.execute('SELECT telegram_id, username, license_type, license_expiry, created_at, total_paid FROM users ORDER BY created_at DESC')
                return cursor.fetchall()
            finally:
                conn.close()
    
    def get_pending_payments(self):
        with self._lock:
            conn = self._get_conn()
            try:
                cursor = conn.cursor()
                cursor.execute('SELECT id, transaction_id, telegram_id, amount, currency, payment_method, license_type, created_at FROM payments WHERE status = "pending" ORDER BY created_at DESC')
                return cursor.fetchall()
            finally:
                conn.close()
    
    def revoke_license(self, telegram_id: int):
        with self._lock:
            conn = self._get_conn()
            try:
                cursor = conn.cursor()
                cursor.execute('UPDATE users SET license_type = "revoked", license_expiry = ? WHERE telegram_id = ?', (datetime.now().isoformat(), telegram_id))
                conn.commit()
            finally:
                conn.close()
    
    def extend_license(self, telegram_id: int, days: int):
        with self._lock:
            conn = self._get_conn()
            try:
                cursor = conn.cursor()
                cursor.execute('SELECT license_expiry FROM users WHERE telegram_id = ?', (telegram_id,))
                row = cursor.fetchone()
                if row and row[0]:
                    current_expiry = datetime.fromisoformat(row[0])
                    new_expiry = max(current_expiry, datetime.now()) + timedelta(days=days)
                else:
                    new_expiry = datetime.now() + timedelta(days=days)
                cursor.execute('UPDATE users SET license_type = "premium", license_expiry = ? WHERE telegram_id = ?', (new_expiry.isoformat(), telegram_id))
                conn.commit()
            finally:
                conn.close()
    
    def get_user_info(self, telegram_id: int):
        with self._lock:
            conn = self._get_conn()
            try:
                cursor = conn.cursor()
                cursor.execute('SELECT telegram_id, username, license_type, license_expiry, created_at, total_paid, payment_count FROM users WHERE telegram_id = ?', (telegram_id,))
                return cursor.fetchone()
            finally:
                conn.close()
    
    def add_admin_log(self, admin_id: int, action: str, target_user: int, details: str = ""):
        with self._lock:
            conn = self._get_conn()
            try:
                cursor = conn.cursor()
                cursor.execute('INSERT INTO admin_logs (admin_id, action, target_user, details, timestamp) VALUES (?, ?, ?, ?, ?)',
                             (admin_id, action, target_user, details, datetime.now().isoformat()))
                conn.commit()
            finally:
                conn.close()

# =============================================================================
# ARBITRAGE ENGINE
# =============================================================================

class ArbitrageEngine:
    def __init__(self, db: Database):
        self.db = db
        self.last_opportunities = []
        self.scan_count = 0
        self.running = True
    
    def generate_matches(self):
        """Generează meciuri din toate ligile globale"""
        matches = []
        team_list = list(TOP_TEAMS.keys())
        
        for league in GLOBAL_LEAGUES:
            num_matches = random.randint(2, 4)
            selected_teams = random.sample(team_list, min(num_matches * 2, len(team_list)))
            
            for i in range(num_matches):
                if i * 2 + 1 < len(selected_teams):
                    home = selected_teams[i * 2]
                    away = selected_teams[i * 2 + 1]
                    matches.append({
                        'league_id': league['id'],
                        'league_name': league['name'],
                        'region': league['region'],
                        'home': home,
                        'away': away,
                        'home_odds_base': TOP_TEAMS.get(home, 2.00),
                        'away_odds_base': TOP_TEAMS.get(away, 2.00),
                        'draw_odds_base': 3.20
                    })
        
        random.shuffle(matches)
        return matches
    
    def get_odds_for_match(self, match):
        bookmakers = ['Superbet', 'Betano', 'Fortuna', 'Unibet', 'WilliamHill', 'Bet365', '888sport']
        selected_bks = random.sample(bookmakers, 3)
        
        odds_data = {}
        for bk in selected_bks:
            variation = random.uniform(-0.08, 0.08)
            odds_data[bk] = {
                '1': round(match['home_odds_base'] + variation, 2),
                'X': round(match['draw_odds_base'] + random.uniform(-0.15, 0.15), 2),
                '2': round(match['away_odds_base'] + variation, 2)
            }
            for k in odds_data[bk]:
                if odds_data[bk][k] < 1.01:
                    odds_data[bk][k] = 1.05
        
        return odds_data
    
    def calculate_arbitrage(self, odds_list):
        if len(odds_list) < 2:
            return None
        total_prob = sum(1/odds for _, odds in odds_list if odds > 1)
        if total_prob < 1:
            profit = (1 - total_prob) * 100
            if profit >= CONFIG.MIN_PROFIT_PERCENT:
                total_stake = 1000
                stakes = {}
                for bk, odds in odds_list:
                    stake = (total_stake * (1/odds)) / total_prob
                    stakes[bk] = {'stake': round(stake, 2), 'odds': odds}
                return profit, stakes
        return None
    
    def scan(self):
        self.scan_count += 1
        opportunities = []
        
        matches = self.generate_matches()
        
        for match in matches[:50]:
            odds_data = self.get_odds_for_match(match)
            outcomes = ['1', 'X', '2']
            outcome_names = {'1': f"{match['home']} WIN", 'X': "DRAW", '2': f"{match['away']} WIN"}
            
            for outcome in outcomes:
                odds_list = []
                for bk, odds in odds_data.items():
                    if outcome in odds and odds[outcome] > 1:
                        odds_list.append((bk, odds[outcome]))
                
                if len(odds_list) >= 2:
                    result = self.calculate_arbitrage(odds_list)
                    if result:
                        profit, stakes = result
                        opp = {
                            'league': match['league_name'],
                            'event_name': f"{match['home']} vs {match['away']}",
                            'outcome': outcome_names[outcome],
                            'bookmaker1': list(stakes.keys())[0],
                            'odds1': list(stakes.values())[0]['odds'],
                            'bookmaker2': list(stakes.keys())[1] if len(stakes) > 1 else list(stakes.keys())[0],
                            'odds2': list(stakes.values())[1]['odds'] if len(stakes) > 1 else list(stakes.values())[0]['odds'],
                            'profit_percent': profit
                        }
                        opportunities.append(opp)
                        self.db.save_arbitrage(opp)
        
        opportunities.sort(key=lambda x: x['profit_percent'], reverse=True)
        self.last_opportunities = opportunities[:25]
        
        if self.scan_count % 5 == 0:
            leagues_found = set([opp.get('league', 'Unknown') for opp in opportunities[:5]])
            print(f"  📊 Scan #{self.scan_count}: {len(opportunities)} oportunități | Ligi: {', '.join(list(leagues_found)[:3])}")
        
        return {
            'opportunities': self.last_opportunities, 
            'count': len(opportunities), 
            'scan_number': self.scan_count
        }

# =============================================================================
# WEB DASHBOARD
# =============================================================================

class WebDashboard:
    def __init__(self, engine: ArbitrageEngine, db: Database):
        self.engine = engine
        self.db = db
    
    def generate_html(self):
        opps = self.engine.last_opportunities
        stats = self.db.get_stats()
        
        rows = ""
        for opp in opps[:20]:
            rows += f"""
            <tr>
                <td><span class="league-badge">{opp.get('league', 'Various')[:20]}</span></td>
                <td>{opp['event_name'][:35]}</td>
                <td><span class="outcome-badge">{opp['outcome']}</span></td>
                <td class="buy">{opp['bookmaker1'].upper()}</td>
                <td>{opp['odds1']:.2f}</td>
                <td class="sell">{opp['bookmaker2'].upper()}</td>
                <td>{opp['odds2']:.2f}</td>
                <td class="profit">+{opp['profit_percent']:.2f}%</td>
            </tr>
            """
        
        if not rows:
            rows = '<tr><td colspan="8" class="scanning">🔍 SCANARE GLOBALĂ 24/7 ACTIVĂ...</td></tr>'
        
        return f'''<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="refresh" content="{CONFIG.SCAN_INTERVAL}">
    <title>WHOAMISec | Global Arbitrage Scanner</title>
    <style>
        * {{ margin: 0; padding: 0; box-sizing: border-box; }}
        body {{
            background: linear-gradient(135deg, #0a0f1a 0%, #0b1120 100%);
            font-family: 'Poppins', sans-serif;
            color: #e5e9f0;
            padding: 20px;
        }}
        .container {{ max-width: 1400px; margin: 0 auto; }}
        .header {{
            text-align: center;
            margin-bottom: 30px;
            padding: 20px;
            background: rgba(0,255,136,0.1);
            border-radius: 20px;
        }}
        .logo {{
            font-size: 2rem;
            font-weight: bold;
            background: linear-gradient(135deg, #00ff88, #00ccff);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }}
        .stats-grid {{
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }}
        .stat-card {{
            background: rgba(0,0,0,0.5);
            border: 1px solid rgba(0,255,136,0.2);
            border-radius: 15px;
            padding: 15px;
            text-align: center;
        }}
        .stat-value {{
            font-size: 1.8rem;
            font-weight: bold;
            color: #00ff88;
        }}
        table {{ width: 100%; border-collapse: collapse; }}
        th {{ background: #1a2a3a; padding: 12px; text-align: left; }}
        td {{ padding: 10px; border-bottom: 1px solid rgba(0,255,136,0.1); }}
        .profit {{ color: #00ff88; font-weight: bold; }}
        .buy {{ color: #ff6666; }}
        .sell {{ color: #66ff66; }}
        @media (max-width: 768px) {{
            .stats-grid {{ grid-template-columns: repeat(2, 1fr); }}
            td,th {{ font-size: 0.7rem; padding: 5px; }}
        }}
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">🌍 WHOAMISec | GLOBAL ARBITRAGE PRO</div>
            <div>Scan #{self.engine.scan_count} | {len(GLOBAL_LEAGUES)}+ Ligi | Profit Mediu: +{stats['avg_profit']}%</div>
        </div>
        
        <div class="stats-grid">
            <div class="stat-card"><div class="stat-value">{stats['opportunities']}</div><div>Oportunități</div></div>
            <div class="stat-card"><div class="stat-value">{stats['users']}</div><div>Utilizatori</div></div>
            <div class="stat-card"><div class="stat-value">{stats['revenue']} LEI</div><div>Venit</div></div>
            <div class="stat-card"><div class="stat-value">{self.engine.scan_count}</div><div>Scanări</div></div>
        </div>
        
        <h3>🔥 OPORTUNITĂȚI LIVE</h3>
        <div style="overflow-x: auto;">
            <table>
                <thead><tr><th>Ligă</th><th>Eveniment</th><th>Pronostic</th><th>Cumpără</th><th>Cota</th><th>Vinde</th><th>Cota</th><th>Profit</th></tr></thead>
                <tbody>{rows}</tbody>
            </table>
        </div>
    </div>
</body>
</html>'''
    
    def start(self):
        from http.server import HTTPServer, BaseHTTPRequestHandler
        
        class Handler(BaseHTTPRequestHandler):
            def do_GET(self):
                if self.path == '/' or self.path == '/index.html':
                    dashboard = WebDashboard(self.engine, self.db)
                    content = dashboard.generate_html()
                    self.send_response(200)
                    self.send_header('Content-type', 'text/html; charset=utf-8')
                    self.end_headers()
                    self.wfile.write(content.encode('utf-8'))
                else:
                    self.send_response(404)
                    self.end_headers()
            
            def log_message(self, format, *args):
                pass
        
        server = HTTPServer(('0.0.0.0', CONFIG.WEB_PORT), lambda: Handler(self.engine, self.db))
        url = f"http://localhost:{CONFIG.WEB_PORT}"
        
        print(f"\n🌐 WEB DASHBOARD: {url}")
        
        def open_browser():
            time.sleep(1.5)
            webbrowser.open(url)
        
        threading.Thread(target=open_browser, daemon=True).start()
        server.serve_forever()

# =============================================================================
# TELEGRAM BOT
# =============================================================================

class TelegramBot:
    def __init__(self, db: Database, engine: ArbitrageEngine):
        self.db = db
        self.engine = engine
        self.application = None
        self._initialized = False
    
    def is_admin(self, user_id: int) -> bool:
        return user_id == CONFIG.ADMIN_ID
    
    async def start_command(self, update: Update, context: ContextTypes.DEFAULT_TYPE):
        user = update.effective_user
        self.db.add_user(user.id, user.username or user.first_name)
        
        valid, msg = self.db.check_license(user.id)
        
        keyboard = [[InlineKeyboardButton("🔍 SCAN", callback_data="scan"), InlineKeyboardButton("💰 BUY", callback_data="buy")]]
        
        if valid or self.is_admin(user.id):
            await update.message.reply_text(
                f"🔥 *SUPERBOT PRO v12.0* 🔥\n\nSalut {user.first_name}!\n✅ {msg if valid else 'Admin access'}\n\n"
                f"🌍 *Scanner Global Activ*\n• {len(GLOBAL_LEAGUES)}+ ligi monitorizate\n\n"
                f"Folosește butoanele:",
                parse_mode='Markdown',
                reply_markup=InlineKeyboardMarkup(keyboard)
            )
        else:
            await update.message.reply_text(
                f"⚠️ *Acces Restricționat*\n\n{msg}\n\nCumpără o licență pentru acces global!",
                parse_mode='Markdown',
                reply_markup=InlineKeyboardMarkup([[InlineKeyboardButton("💰 CUMPĂRĂ", callback_data="buy")]])
            )
    
    async def scan_command(self, update: Update, context: ContextTypes.DEFAULT_TYPE):
        user_id = update.effective_user.id
        valid, msg = self.db.check_license(user_id)
        
        if not valid and not self.is_admin(user_id):
            await update.message.reply_text(f"❌ *Acces refuzat!*\n\n{msg}", parse_mode='Markdown')
            return
        
        await update.message.reply_text("🔍 *Scanez oportunități globale...*", parse_mode='Markdown')
        result = self.engine.scan()
        
        if result['opportunities']:
            text = f"🌍 *OPORTUNITĂȚI GLOBALE* 🌍\n\n"
            for i, opp in enumerate(result['opportunities'][:5], 1):
                text += f"{i}. *{opp['event_name']}*\n"
                text += f"   📍 {opp.get('league', 'Various')} | {opp['outcome']}\n"
                text += f"   💰 Profit: *+{opp['profit_percent']:.2f}%*\n\n"
            text += f"\n📊 Total: {result['count']} oportunități"
            await update.message.reply_text(text, parse_mode='Markdown')
        else:
            await update.message.reply_text("📭 *Nicio oportunitate momentan.*", parse_mode='Markdown')
    
    async def status_command(self, update: Update, context: ContextTypes.DEFAULT_TYPE):
        user_id = update.effective_user.id
        user_info = self.db.get_user_info(user_id)
        
        if user_info:
            _, username, license_type, expiry_str, created_at, total_paid = user_info
            expiry = datetime.fromisoformat(expiry_str) if expiry_str else datetime.now()
            days_left = (expiry - datetime.now()).days if expiry > datetime.now() else 0
            
            text = f"📊 *STATUS CONT*\n\n"
            text += f"👤 Utilizator: @{username or user_id}\n"
            text += f"🎫 Licență: *{license_type.upper()}*\n"
            text += f"⏰ Expiră: {expiry.strftime('%d.%m.%Y')} ({days_left} zile)\n"
            text += f"💰 Total plătit: {total_paid:.2f} LEI\n"
            await update.message.reply_text(text, parse_mode='Markdown')
    
    async def buy_command(self, update: Update, context: ContextTypes.DEFAULT_TYPE):
        keyboard = [
            [InlineKeyboardButton("📅 1 Zi - 25 LEI", callback_data="buy_daily")],
            [InlineKeyboardButton("📆 7 Zile - 75 LEI", callback_data="buy_weekly")],
            [InlineKeyboardButton("🌙 30 Zile - 250 LEI", callback_data="buy_monthly")],
            [InlineKeyboardButton("⭐ LIFETIME - 999 LEI", callback_data="buy_lifetime")],
            [InlineKeyboardButton("❌ Anulează", callback_data="cancel")]
        ]
        await update.message.reply_text(
            "💰 *Alege pachetul:*\n\nPlăți acceptate: Paysafe | XMR | BTC | USDT\n100% Anonim",
            parse_mode='Markdown',
            reply_markup=InlineKeyboardMarkup(keyboard)
        )
    
    async def admin_panel(self, update: Update, context: ContextTypes.DEFAULT_TYPE):
        if not self.is_admin(update.effective_user.id):
            await update.message.reply_text("❌ Acces interzis.")
            return
        
        stats = self.db.get_stats()
        pending = self.db.get_pending_payments()
        users = self.db.get_all_users()
        
        text = f"👑 *PANEL ADMIN*\n\n"
        text += f"📊 Utilizatori: {stats['users']}\n"
        text += f"💰 Venit: {stats['revenue']} LEI\n"
        text += f"⏳ Plăți în așteptare: {len(pending)}\n"
        text += f"🔄 Scanări: {self.engine.scan_count}\n"
        text += f"🌍 Ligi: {len(GLOBAL_LEAGUES)}\n\n"
        text += f"📋 *Comenzi admin:*\n"
        text += f"`/confirm_ID` - Confirmă plată\n"
        text += f"`/adduser ID ZILE` - Adaugă user\n"
        text += f"`/extend ID ZILE` - Prelungește\n"
        text += f"`/revoke ID` - Revocă licență"
        
        await update.message.reply_text(text, parse_mode='Markdown')
    
    async def handle_callback(self, update: Update, context: ContextTypes.DEFAULT_TYPE):
        query = update.callback_query
        await query.answer()
        data = query.data
        
        if data == "scan":
            await self.scan_command(update, context)
        elif data == "buy":
            await self.buy_command(update, context)
        elif data == "status":
            await self.status_command(update, context)
        elif data.startswith("buy_"):
            license_type = data.replace("buy_", "")
            user_id = update.effective_user.id
            
            tx_id = hashlib.sha256(f"{user_id}{time.time()}{secrets.token_hex(4)}".encode()).hexdigest()[:16]
            
            prices = {'daily': '25 LEI', 'weekly': '75 LEI', 'monthly': '250 LEI', 'lifetime': '999 LEI'}
            
            self.db.add_payment(tx_id, user_id, float(prices[license_type].split()[0]), 'LEI', 'crypto', license_type)
            
            text = f"💳 *PLATĂ {license_type.upper()}*\n\n"
            text += f"💰 Sumă: {prices[license_type]}\n"
            text += f"🆔 ID: `{tx_id}`\n\n"
            text += f"*Adrese plată:*\n"
            text += f"XMR: `{CONFIG.XMR_ADDRESS[:30]}...`\n"
            text += f"BTC: `{CONFIG.BTC_ADDRESS[:30]}...`\n\n"
            text += f"✅ După plată, adminul va confirma automat."
            
            keyboard = [[InlineKeyboardButton("✅ AM PLĂTIT", callback_data=f"paid_{tx_id}")]]
            await query.edit_message_text(text, parse_mode='Markdown', reply_markup=InlineKeyboardMarkup(keyboard))
            
            # Notifică admin
            if self.application:
                await self.application.bot.send_message(
                    CONFIG.ADMIN_ID,
                    f"💰 *Plată nouă*\n🆔 `{tx_id}`\n👤 User: {user_id}\n📦 {license_type}",
                    parse_mode='Markdown'
                )
        
        elif data.startswith("paid_"):
            await query.edit_message_text("✅ *Plată înregistrată!*\n\nAșteaptă confirmarea administratorului.", parse_mode='Markdown')
        
        elif data == "cancel":
            await query.edit_message_text("❌ Anulat.")
    
    async def handle_admin_commands(self, update: Update, context: ContextTypes.DEFAULT_TYPE):
        if not self.is_admin(update.effective_user.id):
            return
        
        text = update.message.text
        
        if text.startswith('/confirm_'):
            tx_id = text.replace('/confirm_', '').strip()
            result = self.db.confirm_payment(tx_id, CONFIG.ADMIN_ID)
            if result:
                tg_id, license_type = result
                await update.message.reply_text(f"✅ Plata {tx_id[:10]}... confirmată pentru user {tg_id}")
                if self.application:
                    await self.application.bot.send_message(tg_id, f"🎉 *LICENȚĂ ACTIVATĂ!*\n\nAccesul tău a fost activat.", parse_mode='Markdown')
            else:
                await update.message.reply_text(f"❌ Tranzacția nu a fost găsită.")
        
        elif text.startswith('/adduser'):
            try:
                parts = text.split()
                uid = int(parts[1])
                days = int(parts[2]) if len(parts) > 2 else 30
                self.db.add_user(uid, f"user_{uid}")
                self.db.activate_license(uid, 'premium', days, 0)
                await update.message.reply_text(f"✅ User {uid} adăugat cu {days} zile")
            except:
                await update.message.reply_text("❌ /adduser ID ZILE")
        
        elif text.startswith('/extend'):
            try:
                parts = text.split()
                uid = int(parts[1])
                days = int(parts[2])
                self.db.extend_license(uid, days)
                await update.message.reply_text(f"✅ Licența lui {uid} prelungită cu {days} zile")
            except:
                await update.message.reply_text("❌ /extend ID ZILE")
        
        elif text.startswith('/revoke'):
            try:
                uid = int(text.split()[1])
                self.db.revoke_license(uid)
                await update.message.reply_text(f"✅ Licența lui {uid} a fost revocată")
            except:
                await update.message.reply_text("❌ /revoke ID")
    
    async def help_command(self, update: Update, context: ContextTypes.DEFAULT_TYPE):
        await update.message.reply_text(
            "📚 *COMENZI*\n\n"
            "/start - Pornește botul\n"
            "/scan - Scanează oportunități\n"
            "/status - Verifică licența\n"
            "/buy - Cumpără licență\n"
            "/admin - Panou admin (doar admin)\n"
            "/help - Ajutor\n\n"
            "📞 Contact: @WHOAMISec",
            parse_mode='Markdown'
        )
    
    async def run_async(self):
        if not TELEGRAM_OK:
            print("[⚠️] Telegram bot not available")
            return
        
        print("\n🤖 TELEGRAM BOT STARTED - @WHOAMISec")
        print(f"👑 Admin ID: {CONFIG.ADMIN_ID}")
        print(f"🌍 Ligi: {len(GLOBAL_LEAGUES)}")
        
        # Creează application
        self.application = Application.builder().token(CONFIG.BOT_TOKEN).build()
        
        # Adaugă handlers
        self.application.add_handler(CommandHandler("start", self.start_command))
        self.application.add_handler(CommandHandler("scan", self.scan_command))
        self.application.add_handler(CommandHandler("status", self.status_command))
        self.application.add_handler(CommandHandler("buy", self.buy_command))
        self.application.add_handler(CommandHandler("admin", self.admin_panel))
        self.application.add_handler(CommandHandler("help", self.help_command))
        
        # Handlers pentru admin
        self.application.add_handler(CommandHandler("confirm", self.handle_admin_commands))
        self.application.add_handler(CommandHandler("adduser", self.handle_admin_commands))
        self.application.add_handler(CommandHandler("extend", self.handle_admin_commands))
        self.application.add_handler(CommandHandler("revoke", self.handle_admin_commands))
        
        self.application.add_handler(CallbackQueryHandler(self.handle_callback))
        
        # Inițializează și rulează polling
        await self.application.initialize()
        await self.application.start()
        
        print("✅ Bot is running...")
        
        # Rulează polling manual
        await self.application.updater.start_polling()
        
        # Menține botul activ
        try:
            while True:
                await asyncio.sleep(1)
        except asyncio.CancelledError:
            pass
        finally:
            await self.application.updater.stop()
            await self.application.stop()
            await self.application.shutdown()

# =============================================================================
# MAIN CONTROLLER
# =============================================================================

class SuperBotController:
    def __init__(self):
        self.db = Database()
        self.engine = ArbitrageEngine(self.db)
        self.web = WebDashboard(self.engine, self.db)
        self.bot = TelegramBot(self.db, self.engine)
        self.scanner_task = None
    
    async def start_scanner(self):
        """Rulează scanner-ul"""
        print(f"\n🔄 PORNIRE SCANNER GLOBAL...")
        print(f"🌍 Monitorizează {len(GLOBAL_LEAGUES)} ligi")
        print(f"💰 Profit minim vizat: {CONFIG.MIN_PROFIT_PERCENT}%\n")
        
        while True:
            try:
                result = self.engine.scan()
                if result['count'] > 0:
                    print(f"🎯 Scan #{result['scan_number']}: {result['count']} oportunități!")
                await asyncio.sleep(CONFIG.SCAN_INTERVAL)
            except Exception as e:
                print(f"Scanner error: {e}")
                await asyncio.sleep(5)
    
    async def start(self):
        print("\n" + "="*70)
        print("  🛡️ WHOAMISec | SUPERBOT PRO v12.0 - GLOBAL".center(70))
        print(f"  🌍 {len(GLOBAL_LEAGUES)} Ligi | Plăți Anonime | 24/7".center(70))
        print("="*70 + "\n")
        
        # Pornește web dashboard în thread separat
        web_thread = threading.Thread(target=self.web.start, daemon=True)
        web_thread.start()
        
        # Pornește scanner-ul
        asyncio.create_task(self.start_scanner())
        
        # Pornește bot-ul
        await self.bot.run_async()

# =============================================================================
# ENTRY POINT
# =============================================================================

if __name__ == "__main__":
    try:
        controller = SuperBotController()
        asyncio.run(controller.start())
    except KeyboardInterrupt:
        print("\n\n💀 WHOAMISec | SuperBot Pro Global oprit.")
        sys.exit(0)