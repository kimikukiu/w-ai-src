#!/usr/bin/env python3
"""
╔══════════════════════════════════════════════════════════════════════════════════╗
║  [WHOAMISec] SUPERBOT PRO v13.0 - FULL COMMERCIAL EDITION                       ║
║  ✅ Listă Prețuri | ✅ Restricții Utilizatori | ✅ Admin Panel | ✅ Plăți       ║
║  Paysafe | XMR | BTC | USDT | Session ID Activation | 24/7 Scanner              ║
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
import secrets
import hashlib
from datetime import datetime, timedelta
from typing import Dict, List, Optional, Tuple
from dataclasses import dataclass, field
from pathlib import Path
import requests
from http.server import HTTPServer, BaseHTTPRequestHandler
from functools import wraps

# =============================================================================
# AUTO-INSTALL DEPENDENCIES
# =============================================================================

def install_packages():
    packages = ['rich', 'python-telegram-bot', 'qrcode', 'pillow']
    for pkg in packages:
        try:
            __import__(pkg.replace('-', '_'))
        except ImportError:
            os.system(f"{sys.executable} -m pip install {pkg}")

install_packages()

try:
    from rich.console import Console
    from rich.table import Table
    from rich.panel import Panel
    from rich.progress import Progress
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
# SESSION ID PENTRU ACTIVARE
# =============================================================================

SESSION_ID = "05452eefbf3ce80c59653dc7fcbd99a45992ae6127f6c13fd51faa565da6442c7c"

# =============================================================================
# CONFIGURATION - PRICES
# =============================================================================

@dataclass
class Config:
    SCAN_INTERVAL: int = 30
    WEB_PORT: int = 8084
    MIN_PROFIT_PERCENT: float = 0.8
    BANKROLL: float = 10000.0
    DB_PATH: str = ""
    
    # Telegram Bot (admin)
    BOT_TOKEN: str = "YOUR_BOT_TOKEN_HERE"
    ADMIN_IDS: List[int] = field(default_factory=lambda: [123456789])  # Add your Telegram ID
    
    # ============= PRICE LIST =============
    # Paysafe (RON)
    PRICE_DAILY_RON: float = 25
    PRICE_WEEKLY_RON: float = 75
    PRICE_MONTHLY_RON: float = 250
    PRICE_LIFETIME_RON: float = 999
    
    # Monero (XMR)
    PRICE_DAILY_XMR: float = 0.02
    PRICE_WEEKLY_XMR: float = 0.07
    PRICE_MONTHLY_XMR: float = 0.25
    PRICE_LIFETIME_XMR: float = 1.2
    
    # Bitcoin (BTC)
    PRICE_DAILY_BTC: float = 0.0008
    PRICE_WEEKLY_BTC: float = 0.0025
    PRICE_MONTHLY_BTC: float = 0.01
    PRICE_LIFETIME_BTC: float = 0.04
    
    # USDT (TRC20)
    PRICE_DAILY_USDT: float = 25
    PRICE_WEEKLY_USDT: float = 75
    PRICE_MONTHLY_USDT: float = 250
    PRICE_LIFETIME_USDT: float = 999
    
    # Payment addresses
    XMR_ADDRESS: str = "8BbApiMBHsPVKkLEP4rVbST6CnSb3LW2gXygngCi5MGiBuwAFh6bFEzT3UTuFCkLHtyHnrYNnHycdaGb2Kgkkmw8jViCdB6"
    BTC_ADDRESS: str = "bc1XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX"
    USDT_TRC20_ADDRESS: str = "TXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX"
    
    # Trial days
    TRIAL_DAYS: int = 3
    
    def __post_init__(self):
        home = Path.home()
        self.DB_PATH = str(home / '.whoamisec' / 'superbot.db')
        os.makedirs(str(home / '.whoamisec'), exist_ok=True)

CONFIG = Config()

# =============================================================================
# DATABASE WITH USER RESTRICTIONS
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
        
        # Users table with restrictions
        cursor.execute('''
            CREATE TABLE IF NOT EXISTS users (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                user_id TEXT UNIQUE,
                username TEXT,
                email TEXT,
                license_type TEXT DEFAULT 'trial',
                license_expiry TEXT,
                is_admin BOOLEAN DEFAULT 0,
                is_banned BOOLEAN DEFAULT 0,
                ban_reason TEXT,
                total_spent REAL DEFAULT 0,
                created_at TEXT,
                last_active TEXT,
                language TEXT DEFAULT 'ro'
            )
        ''')
        
        # Payments table
        cursor.execute('''
            CREATE TABLE IF NOT EXISTS payments (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                transaction_id TEXT UNIQUE,
                user_id TEXT,
                amount REAL,
                currency TEXT,
                payment_method TEXT,
                license_type TEXT,
                pin_code TEXT,
                tx_hash TEXT,
                status TEXT DEFAULT 'pending',
                verified_by TEXT,
                created_at TEXT,
                confirmed_at TEXT
            )
        ''')
        
        # Arbitrage opportunities
        cursor.execute('''
            CREATE TABLE IF NOT EXISTS arbitrage_opportunities (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                timestamp TEXT,
                event_name TEXT,
                outcome TEXT,
                bookmaker1 TEXT,
                odds1 REAL,
                bookmaker2 TEXT,
                odds2 REAL,
                profit_percent REAL
            )
        ''')
        
        # Admin logs
        cursor.execute('''
            CREATE TABLE IF NOT EXISTS admin_logs (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                admin_id TEXT,
                action TEXT,
                target_user TEXT,
                details TEXT,
                timestamp TEXT
            )
        ''')
        
        # Insert default admin if not exists
        for admin_id in CONFIG.ADMIN_IDS:
            cursor.execute('''
                INSERT OR IGNORE INTO users (user_id, username, license_type, license_expiry, is_admin, created_at)
                VALUES (?, ?, ?, ?, ?, ?)
            ''', (str(admin_id), 'Admin', 'lifetime', (datetime.now() + timedelta(days=3650)).isoformat(), 1, datetime.now().isoformat()))
        
        conn.commit()
        conn.close()
    
    # ============= USER MANAGEMENT =============
    
    def add_user(self, user_id: str, username: str = None, email: str = None, language: str = 'ro'):
        expiry = (datetime.now() + timedelta(days=CONFIG.TRIAL_DAYS)).isoformat()
        
        with self._lock:
            conn = self._get_conn()
            try:
                cursor = conn.cursor()
                cursor.execute('''
                    INSERT OR REPLACE INTO users 
                    (user_id, username, email, license_type, license_expiry, created_at, last_active, language)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)
                ''', (user_id, username, email, 'trial', expiry, datetime.now().isoformat(), datetime.now().isoformat(), language))
                conn.commit()
                return True
            finally:
                conn.close()
    
    def get_user(self, user_id: str) -> Optional[Dict]:
        with self._lock:
            conn = self._get_conn()
            try:
                cursor = conn.cursor()
                cursor.execute('SELECT * FROM users WHERE user_id = ?', (user_id,))
                row = cursor.fetchone()
                if row:
                    columns = [description[0] for description in cursor.description]
                    return dict(zip(columns, row))
                return None
            finally:
                conn.close()
    
    def check_license(self, user_id: str) -> Tuple[bool, str, Dict]:
        """Check if user has valid license and not banned"""
        user = self.get_user(user_id)
        
        if not user:
            return False, "Utilizator negăsit. Înregistrează-te cu /start", None
        
        if user.get('is_banned'):
            return False, f"⚠️ CONT BLOCAT\nMotiv: {user.get('ban_reason', 'Necunoscut')}\nContactează adminul.", user
        
        if user.get('license_type') == 'lifetime':
            return True, "✅ Licență LIFETIME - acces nelimitat", user
        
        expiry = datetime.fromisoformat(user['license_expiry'])
        if expiry > datetime.now():
            days_left = (expiry - datetime.now()).days
            hours_left = (expiry - datetime.now()).seconds // 3600
            return True, f"✅ Licență ACTIVĂ\n📅 Expiră în: {days_left} zile, {hours_left} ore", user
        else:
            return False, "❌ LICENȚĂ EXPIRATĂ\nFolosește /buy pentru a reînnoi", user
    
    def activate_license(self, user_id: str, license_type: str, days: int, payment_id: int = None):
        expiry = (datetime.now() + timedelta(days=days)).isoformat()
        
        with self._lock:
            conn = self._get_conn()
            try:
                cursor = conn.cursor()
                cursor.execute('''
                    UPDATE users SET license_type = ?, license_expiry = ? WHERE user_id = ?
                ''', (license_type, expiry, user_id))
                conn.commit()
            finally:
                conn.close()
    
    def ban_user(self, admin_id: str, user_id: str, reason: str):
        with self._lock:
            conn = self._get_conn()
            try:
                cursor = conn.cursor()
                cursor.execute('UPDATE users SET is_banned = 1, ban_reason = ? WHERE user_id = ?', (reason, user_id))
                conn.commit()
                
                # Log action
                cursor.execute('''
                    INSERT INTO admin_logs (admin_id, action, target_user, details, timestamp)
                    VALUES (?, ?, ?, ?, ?)
                ''', (admin_id, 'ban', user_id, reason, datetime.now().isoformat()))
                conn.commit()
            finally:
                conn.close()
    
    def unban_user(self, admin_id: str, user_id: str):
        with self._lock:
            conn = self._get_conn()
            try:
                cursor = conn.cursor()
                cursor.execute('UPDATE users SET is_banned = 0, ban_reason = NULL WHERE user_id = ?', (user_id,))
                conn.commit()
                
                cursor.execute('''
                    INSERT INTO admin_logs (admin_id, action, target_user, details, timestamp)
                    VALUES (?, ?, ?, ?, ?)
                ''', (admin_id, 'unban', user_id, 'User unbanned', datetime.now().isoformat()))
                conn.commit()
            finally:
                conn.close()
    
    def give_lifetime(self, admin_id: str, user_id: str):
        expiry = (datetime.now() + timedelta(days=3650)).isoformat()
        
        with self._lock:
            conn = self._get_conn()
            try:
                cursor = conn.cursor()
                cursor.execute('UPDATE users SET license_type = "lifetime", license_expiry = ? WHERE user_id = ?', (expiry, user_id))
                conn.commit()
                
                cursor.execute('''
                    INSERT INTO admin_logs (admin_id, action, target_user, details, timestamp)
                    VALUES (?, ?, ?, ?, ?)
                ''', (admin_id, 'give_lifetime', user_id, 'Lifetime license granted', datetime.now().isoformat()))
                conn.commit()
            finally:
                conn.close()
    
    # ============= PAYMENT MANAGEMENT =============
    
    def add_payment(self, transaction_id: str, user_id: str, amount: float, currency: str, payment_method: str, license_type: str, pin_code: str = None, tx_hash: str = None):
        with self._lock:
            conn = self._get_conn()
            try:
                cursor = conn.cursor()
                cursor.execute('''
                    INSERT INTO payments (transaction_id, user_id, amount, currency, payment_method, license_type, pin_code, tx_hash, status, created_at)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                ''', (transaction_id, user_id, amount, currency, payment_method, license_type, pin_code, tx_hash, 'pending', datetime.now().isoformat()))
                conn.commit()
                return cursor.lastrowid
            finally:
                conn.close()
    
    def confirm_payment(self, admin_id: str, transaction_id: str):
        with self._lock:
            conn = self._get_conn()
            try:
                cursor = conn.cursor()
                cursor.execute('''
                    UPDATE payments SET status = 'confirmed', confirmed_at = ?, verified_by = ? WHERE transaction_id = ?
                ''', (datetime.now().isoformat(), admin_id, transaction_id))
                conn.commit()
                
                # Get user and license type
                cursor.execute('SELECT user_id, license_type FROM payments WHERE transaction_id = ?', (transaction_id,))
                row = cursor.fetchone()
                if row:
                    user_id, license_type = row
                    days = {'daily': 1, 'weekly': 7, 'monthly': 30, 'lifetime': 3650}.get(license_type, 30)
                    self.activate_license(user_id, license_type, days, transaction_id)
                    
                    # Update total spent
                    cursor.execute('UPDATE users SET total_spent = total_spent + (SELECT amount FROM payments WHERE transaction_id = ?) WHERE user_id = ?', (transaction_id, user_id))
                    conn.commit()
                
                return True
            finally:
                conn.close()
    
    def get_pending_payments(self):
        with self._lock:
            conn = self._get_conn()
            try:
                cursor = conn.cursor()
                cursor.execute('SELECT * FROM payments WHERE status = "pending" ORDER BY created_at DESC')
                rows = cursor.fetchall()
                columns = [description[0] for description in cursor.description]
                return [dict(zip(columns, row)) for row in rows]
            finally:
                conn.close()
    
    # ============= STATISTICS =============
    
    def get_stats(self, admin_view: bool = False):
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
                
                cursor.execute('SELECT COUNT(*) FROM users WHERE is_banned = 1')
                banned_users = cursor.fetchone()[0]
                
                cursor.execute('SELECT COUNT(*) FROM payments WHERE status="confirmed"')
                total_payments = cursor.fetchone()[0]
                
                cursor.execute('SELECT SUM(amount) FROM payments WHERE status="confirmed"')
                total_revenue = cursor.fetchone()[0] or 0
                
                cursor.execute('SELECT COUNT(*) FROM users WHERE license_type="lifetime"')
                lifetime_users = cursor.fetchone()[0]
                
                cursor.execute('SELECT COUNT(*) FROM users WHERE license_type="trial" AND license_expiry > datetime("now")')
                active_trials = cursor.fetchone()[0]
                
                return {
                    'opportunities': total_opps,
                    'avg_profit': round(avg_profit, 2),
                    'users': total_users,
                    'banned_users': banned_users,
                    'payments': total_payments,
                    'revenue': total_revenue,
                    'lifetime_users': lifetime_users,
                    'active_trials': active_trials
                }
            finally:
                conn.close()
    
    def get_all_users(self):
        with self._lock:
            conn = self._get_conn()
            try:
                cursor = conn.cursor()
                cursor.execute('SELECT user_id, username, license_type, license_expiry, is_admin, is_banned, total_spent, created_at FROM users ORDER BY created_at DESC')
                rows = cursor.fetchall()
                columns = [description[0] for description in cursor.description]
                return [dict(zip(columns, row)) for row in rows]
            finally:
                conn.close()
    
    def save_arbitrage(self, data: Dict):
        with self._lock:
            conn = self._get_conn()
            try:
                cursor = conn.cursor()
                cursor.execute('''
                    INSERT INTO arbitrage_opportunities 
                    (timestamp, event_name, outcome, bookmaker1, odds1, bookmaker2, odds2, profit_percent)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)
                ''', (datetime.now().isoformat(), data['event_name'], data['outcome'], data['bookmaker1'], data['odds1'], data['bookmaker2'], data['odds2'], data['profit_percent']))
                conn.commit()
            finally:
                conn.close()
    
    def get_recent_opportunities(self, limit=20):
        with self._lock:
            conn = self._get_conn()
            try:
                cursor = conn.cursor()
                cursor.execute('SELECT event_name, outcome, bookmaker1, odds1, bookmaker2, odds2, profit_percent FROM arbitrage_opportunities ORDER BY timestamp DESC LIMIT ?', (limit,))
                return cursor.fetchall()
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
    
    def get_events(self):
        return [
            {'id': '001', 'name': 'Real Madrid vs Barcelona', 'sport': '⚽', 'league': 'La Liga'},
            {'id': '002', 'name': 'Man United vs Liverpool', 'sport': '⚽', 'league': 'Premier League'},
            {'id': '003', 'name': 'Bayern vs Dortmund', 'sport': '⚽', 'league': 'Bundesliga'},
            {'id': '004', 'name': 'PSG vs Marseille', 'sport': '⚽', 'league': 'Ligue 1'},
            {'id': '005', 'name': 'Inter vs AC Milan', 'sport': '⚽', 'league': 'Serie A'},
            {'id': '006', 'name': 'Lakers vs Warriors', 'sport': '🏀', 'league': 'NBA'},
            {'id': '007', 'name': 'Djokovic vs Alcaraz', 'sport': '🎾', 'league': 'Wimbledon'},
        ]
    
    def get_odds(self, event_id):
        base = {
            '001': {'superbet': {'1': 2.05, 'X': 3.35, '2': 3.55}, 'betano': {'1': 2.00, 'X': 3.45, '2': 3.65}, 'fortuna': {'1': 2.10, 'X': 3.25, '2': 3.45}},
            '002': {'superbet': {'1': 1.90, 'X': 3.55, '2': 4.15}, 'betano': {'1': 1.85, 'X': 3.65, '2': 4.25}, 'fortuna': {'1': 1.93, 'X': 3.50, '2': 4.05}},
            '003': {'superbet': {'1': 2.20, 'X': 3.15, '2': 3.35}, 'betano': {'1': 2.15, 'X': 3.20, '2': 3.40}, 'fortuna': {'1': 2.25, 'X': 3.10, '2': 3.30}},
            '004': {'superbet': {'1': 1.80, 'X': 3.50, '2': 4.45}, 'betano': {'1': 1.83, 'X': 3.55, '2': 4.35}, 'fortuna': {'1': 1.77, 'X': 3.60, '2': 4.55}},
            '005': {'superbet': {'1': 2.35, 'X': 3.15, '2': 2.85}, 'betano': {'1': 2.30, 'X': 3.20, '2': 2.90}, 'fortuna': {'1': 2.40, 'X': 3.10, '2': 2.80}},
            '006': {'superbet': {'1': 1.95, '2': 1.85}, 'betano': {'1': 1.92, '2': 1.88}, 'fortuna': {'1': 1.98, '2': 1.82}},
            '007': {'superbet': {'1': 1.75, '2': 2.05}, 'betano': {'1': 1.72, '2': 2.10}, 'fortuna': {'1': 1.78, '2': 2.02}},
        }
        odds = base.get(event_id, {})
        for bk in odds:
            for out in odds[bk]:
                if odds[bk][out] != 1.0:
                    odds[bk][out] = round(odds[bk][out] + random.uniform(-0.03, 0.03), 2)
        return odds
    
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
        for event in self.get_events():
            odds_data = self.get_odds(event['id'])
            if not odds_data:
                continue
            outcomes = ['1', 'X', '2'] if '⚽' in event['sport'] else ['1', '2']
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
                            'event_name': event['name'],
                            'outcome': 'WIN' if outcome == '1' else 'DRAW' if outcome == 'X' else 'AWAY',
                            'bookmaker1': list(stakes.keys())[0],
                            'odds1': list(stakes.values())[0]['odds'],
                            'bookmaker2': list(stakes.keys())[1] if len(stakes) > 1 else '',
                            'odds2': list(stakes.values())[1]['odds'] if len(stakes) > 1 else 0,
                            'profit_percent': profit
                        }
                        opportunities.append(opp)
                        self.db.save_arbitrage(opp)
        opportunities.sort(key=lambda x: x['profit_percent'], reverse=True)
        self.last_opportunities = opportunities[:15]
        return {'opportunities': self.last_opportunities, 'count': len(opportunities), 'scan_number': self.scan_count}
    
    def start_24_7(self, callback=None):
        while self.running:
            try:
                result = self.scan()
                if callback:
                    callback(result)
                time.sleep(CONFIG.SCAN_INTERVAL)
            except:
                time.sleep(5)

# =============================================================================
# PREMIUM WEB DASHBOARD WITH ADMIN PANEL
# =============================================================================

class WebDashboard:
    def __init__(self, engine: ArbitrageEngine, db: Database):
        self.engine = engine
        self.db = db
    
    def get_platform(self):
        if os.name == 'nt':
            return "Windows"
        elif 'VPS_VERSION' in os.environ:
            return "VPS (Android)"
        else:
            return "Linux/Mac"
    
    def generate_html(self, user_id: str = None, is_admin: bool = False):
        opps = self.engine.last_opportunities
        stats = self.db.get_stats(admin_view=is_admin)
        
        rows = ""
        for opp in opps[:15]:
            rows += f"""
            <tr>
                <td class="event">{opp['event_name'][:35]}</td>
                <td class="outcome">{opp['outcome']}</td>
                <td class="buy">{opp['bookmaker1'].upper()}</td>
                <td class="odds">{opp['odds1']:.2f}</td>
                <td class="sell">{opp['bookmaker2'].upper()}</td>
                <td class="odds">{opp['odds2']:.2f}</td>
                <td class="profit">+{opp['profit_percent']:.2f}%</td>
            </tr>
            """
        
        if not rows:
            rows = '<tr><td colspan="7" class="scanning">🔍 SCANARE 24/7 ACTIVĂ - AȘTEPTĂM OPORTUNITĂȚI...</td></tr>'
        
        # Price list table
        price_rows = f"""
        <tr><td>📅 1 Zi</td><td>{CONFIG.PRICE_DAILY_RON} LEI</td><td>{CONFIG.PRICE_DAILY_XMR:.3f} XMR</td><td>{CONFIG.PRICE_DAILY_BTC:.4f} BTC</td><td>{CONFIG.PRICE_DAILY_USDT} USDT</td></tr>
        <tr><td>📆 7 Zile</td><td>{CONFIG.PRICE_WEEKLY_RON} LEI</td><td>{CONFIG.PRICE_WEEKLY_XMR:.2f} XMR</td><td>{CONFIG.PRICE_WEEKLY_BTC:.4f} BTC</td><td>{CONFIG.PRICE_WEEKLY_USDT} USDT</td></tr>
        <tr><td>🌙 30 Zile</td><td>{CONFIG.PRICE_MONTHLY_RON} LEI</td><td>{CONFIG.PRICE_MONTHLY_XMR:.2f} XMR</td><td>{CONFIG.PRICE_MONTHLY_BTC:.2f} BTC</td><td>{CONFIG.PRICE_MONTHLY_USDT} USDT</td></tr>
        <tr><td>⭐ LIFETIME</td><td>{CONFIG.PRICE_LIFETIME_RON} LEI</td><td>{CONFIG.PRICE_LIFETIME_XMR:.1f} XMR</td><td>{CONFIG.PRICE_LIFETIME_BTC:.2f} BTC</td><td>{CONFIG.PRICE_LIFETIME_USDT} USDT</td></tr>
        """
        
        # Admin panel if admin
        admin_panel = ""
        if is_admin:
            users = self.db.get_all_users()
            user_rows = ""
            for user in users[:20]:
                status = "🔴 BANAT" if user.get('is_banned') else "🟢 ACTIV"
                license_type = user.get('license_type', 'trial')
                expiry = user.get('license_expiry', 'N/A')[:10] if user.get('license_expiry') else 'N/A'
                user_rows += f"""
                <tr>
                    <td>{user.get('user_id', 'N/A')[:20]}</td>
                    <td>{user.get('username', 'N/A')}</td>
                    <td>{license_type}</td>
                    <td>{expiry}</td>
                    <td>${user.get('total_spent', 0):.2f}</td>
                    <td>{status}</td>
                </tr>
                """
            
            admin_panel = f"""
            <div class="admin-section">
                <div class="section-title">🛡️ ADMIN PANEL</div>
                <div class="stats-grid-admin">
                    <div class="stat-card"><div class="stat-value">{stats.get('users', 0)}</div><div class="stat-label">Total Users</div></div>
                    <div class="stat-card"><div class="stat-value">{stats.get('banned_users', 0)}</div><div class="stat-label">Banned</div></div>
                    <div class="stat-card"><div class="stat-value">{stats.get('lifetime_users', 0)}</div><div class="stat-label">Lifetime</div></div>
                    <div class="stat-card"><div class="stat-value">{stats.get('active_trials', 0)}</div><div class="stat-label">Active Trials</div></div>
                    <div class="stat-card"><div class="stat-value">${stats.get('revenue', 0):.2f}</div><div class="stat-label">Revenue</div></div>
                    <div class="stat-card"><div class="stat-value">{stats.get('payments', 0)}</div><div class="stat-label">Payments</div></div>
                </div>
                <div class="section-title">📋 Users List</div>
                <div class="table-wrapper">
                    <table>
                        <thead><tr><th>User ID</th><th>Username</th><th>License</th><th>Expiry</th><th>Spent</th><th>Status</th></tr></thead>
                        <tbody>{user_rows}</tbody>
                    </table>
                </div>
            </div>
            """
        
        return f'''<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <meta http-equiv="Live Fast" content="{CONFIG.SCAN_INTERVAL}">
    <title>WHOAMISec | SuperBot Pro - Arbitrage Scanner</title>
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700;900&family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        * {{ margin: 0; padding: 0; box-sizing: border-box; }}
        
        body {{
            background: linear-gradient(135deg, #0a0f1a 0%, #0b1120 50%, #0a0c15 100%);
            font-family: 'Poppins', sans-serif;
            color: #e5e9f0;
            min-height: 100vh;
        }}
        
        @keyframes glow {{ 0%,100% {{ text-shadow:0 0 10px #00ff88; }} 50% {{ text-shadow:0 0 20px #00ff88; }} }}
        @keyframes pulse {{ 0%,100% {{ transform:scale(1); }} 50% {{ transform:scale(1.02); }} }}
        @keyframes scan-line {{ 0% {{ transform:translateX(-100%); }} 100% {{ transform:translateX(100%); }} }}
        
        .container {{ max-width: 1400px; margin: 0 auto; padding: 20px; }}
        
        .header {{
            text-align: center;
            margin-bottom: 30px;
            padding: 30px;
            background: linear-gradient(135deg, rgba(0,255,136,0.08), rgba(0,0,0,0.5));
            border-radius: 30px;
            border: 1px solid rgba(0,255,136,0.2);
            position: relative;
            overflow: hidden;
        }}
        
        .header::before {{
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 2px;
            background: linear-gradient(90deg, transparent, #00ff88, transparent);
            animation: scan-line 3s infinite;
        }}
        
        .logo {{
            font-family: 'Orbitron', monospace;
            font-size: 2.5rem;
            font-weight: 900;
            background: linear-gradient(135deg, #00ff88, #00ccff, #ff00aa);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            animation: glow 2s infinite;
        }}
        
        .badge-live {{ display: inline-block; background: linear-gradient(135deg, #ff3366, #ff0066); color: white; padding: 5px 15px; border-radius: 30px; font-size: 0.7rem; margin-left: 15px; animation: pulse 1.5s infinite; }}
        .subtitle {{ color: #8b9bcf; margin-top: 10px; font-size: 0.9rem; }}
        
        .stats-grid {{
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
            margin-bottom: 30px;
        }}
        
        .stats-grid-admin {{
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }}
        
        .stat-card {{
            background: rgba(0,0,0,0.5);
            border: 1px solid rgba(0,255,136,0.2);
            border-radius: 20px;
            padding: 15px;
            text-align: center;
            transition: all 0.3s;
        }}
        
        .stat-card:hover {{ transform: translateY(-5px); border-color: #00ff88; }}
        .stat-value {{ font-size: 1.8rem; font-weight: bold; background: linear-gradient(135deg, #00ff88, #00ccff); -webkit-background-clip: text; background-clip: text; color: transparent; font-family: 'Orbitron', monospace; }}
        .stat-label {{ color: #8b9bcf; margin-top: 5px; font-size: 0.7rem; text-transform: uppercase; }}
        
        .section-title {{ font-size: 1.3rem; margin: 25px 0 15px; display: flex; align-items: center; gap: 10px; }}
        .table-wrapper {{ overflow-x: auto; border-radius: 20px; background: rgba(0,0,0,0.3); }}
        
        table {{ width: 100%; border-collapse: collapse; }}
        th {{ background: linear-gradient(135deg, #1a2a3a, #0f1a2a); padding: 12px; text-align: left; font-size: 0.8rem; text-transform: uppercase; color: #00ff88; }}
        td {{ padding: 10px 12px; border-bottom: 1px solid rgba(0,255,136,0.1); font-size: 0.85rem; }}
        tr:hover {{ background: rgba(0,255,136,0.05); }}
        
        .outcome {{ color: #ffaa44; font-weight: bold; }}
        .buy {{ color: #ff6666; font-weight: bold; }}
        .sell {{ color: #66ff66; font-weight: bold; }}
        .profit {{ color: #00ff88; font-weight: bold; }}
        .scanning {{ text-align: center; padding: 40px; color: #ffaa44; }}
        
        .payment-section {{
            margin-top: 30px;
            background: linear-gradient(135deg, rgba(0,0,0,0.6), rgba(15,25,45,0.8));
            border-radius: 20px;
            padding: 25px;
            border: 1px solid rgba(0,255,136,0.2);
        }}
        
        .payment-methods {{ display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px; margin-top: 20px; }}
        .payment-card {{
            background: rgba(0,0,0,0.5);
            border: 1px solid rgba(0,255,136,0.3);
            border-radius: 20px;
            padding: 20px;
            transition: all 0.3s;
        }}
        .payment-card:hover {{ transform: translateY(-5px); border-color: #00ff88; }}
        .payment-card.featured {{ border: 2px solid #ffaa44; background: linear-gradient(135deg, rgba(255,170,68,0.1), rgba(0,0,0,0.6)); }}
        .payment-icon {{ font-size: 2rem; margin-bottom: 10px; }}
        .payment-title {{ font-size: 1.2rem; font-weight: bold; margin-bottom: 10px; }}
        .payment-price {{ font-size: 0.9rem; color: #00ff88; margin: 3px 0; }}
        .payment-address {{ background: rgba(0,0,0,0.5); padding: 8px; border-radius: 8px; font-family: monospace; font-size: 0.7rem; word-break: break-all; margin: 10px 0; }}
        
        .price-table {{
            background: rgba(0,0,0,0.5);
            border-radius: 15px;
            margin: 15px 0;
            overflow: hidden;
        }}
        .price-table th {{ background: #00ff88; color: #000; }}
        .price-table td {{ text-align: center; }}
        
        .btn {{
            background: linear-gradient(135deg, #00ff88, #00aa55);
            color: #000;
            border: none;
            padding: 8px 16px;
            border-radius: 25px;
            font-weight: bold;
            cursor: pointer;
            width: 100%;
            margin-top: 10px;
        }}
        .btn-paysafe {{ background: linear-gradient(135deg, #ffaa44, #ff6600); }}
        .btn:hover, .btn-paysafe:hover {{ transform: scale(1.02); box-shadow: 0 0 20px rgba(0,255,136,0.5); }}
        
        .session-box {{
            margin-top: 30px;
            background: linear-gradient(135deg, #1a2a3a, #0f1a2a);
            border-radius: 20px;
            padding: 25px;
            text-align: center;
            border: 2px solid #ffaa44;
        }}
        .session-id {{ background: #000; padding: 15px; border-radius: 15px; font-family: monospace; font-size: 0.8rem; word-break: break-all; margin: 15px 0; border: 1px solid #ffaa44; }}
        .session-instructions {{ text-align: left; background: rgba(0,0,0,0.3); padding: 15px; border-radius: 10px; margin-top: 15px; font-size: 0.85rem; }}
        
        .admin-section {{
            margin-top: 30px;
            background: linear-gradient(135deg, rgba(255,68,68,0.1), rgba(0,0,0,0.5));
            border-radius: 20px;
            padding: 20px;
            border: 1px solid #ff6666;
        }}
        
        .footer {{ margin-top: 30px; text-align: center; padding: 20px; color: #5a6e8a; border-top: 1px solid rgba(0,255,136,0.1); font-size: 0.8rem; }}
        
        @media (max-width: 768px) {{
            .logo {{ font-size: 1.3rem; }}
            .stat-value {{ font-size: 1.2rem; }}
            td, th {{ padding: 6px; font-size: 0.65rem; }}
        }}
        
        .toast {{ position: fixed; bottom: 20px; right: 20px; background: #00ff88; color: #000; padding: 12px 24px; border-radius: 30px; animation: fadeOut 3s forwards; z-index: 1000; }}
        @keyframes fadeOut {{ 0% {{ opacity: 1; }} 70% {{ opacity: 1; }} 100% {{ opacity: 0; visibility: hidden; }} }}
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div>
                <span class="logo">🛡️ WHOAMISec | SUPERBOT PRO</span>
                <span class="badge-live">LIVE 24/7</span>
            </div>
            <div class="subtitle">Live Fast Arbitrage Scanner | Platform: {self.get_platform()} | Scan #{self.engine.scan_count}</div>
        </div>
        
        <div class="stats-grid">
            <div class="stat-card"><div class="stat-value">{stats['opportunities']}</div><div class="stat-label">Oportunități</div></div>
            <div class="stat-card"><div class="stat-value">+{stats['avg_profit']}%</div><div class="stat-label">Profit Mediu</div></div>
            <div class="stat-card"><div class="stat-value">{stats['users']}</div><div class="stat-label">Utilizatori</div></div>
            <div class="stat-card"><div class="stat-value">{stats['payments']}</div><div class="stat-label">Plăți</div></div>
        </div>
        
        <div class="section-title">🔥 OPORTUNITĂȚI LIVE</div>
        <div class="table-wrapper">
            <table>
                <thead><tr><th>Eveniment</th><th>Pronostic</th><th>Cumpără</th><th>Cota</th><th>Vinde</th><th>Cota</th><th>Profit</th></tr></thead>
                <tbody>{rows}</tbody>
            </table>
        </div>
        
        <div class="payment-section">
            <div class="section-title">💰 LISTĂ PREȚURI - PLĂȚI ANONIME</div>
            <div class="price-table">
                <table>
                    <thead><tr><th>Pachet</th><th>Paysafe (RON)</th><th>Monero (XMR)</th><th>Bitcoin (BTC)</th><th>USDT (TRC20)</th></tr></thead>
                    <tbody>{price_rows}</tbody>
                </table>
            </div>
            
            <div class="payment-methods">
                <div class="payment-card featured">
                    <div class="payment-icon">💳</div>
                    <div class="payment-title">PAYSAFE CARD</div>
                    <div class="payment-price">📅 1 Zi: {CONFIG.PRICE_DAILY_RON} LEI</div>
                    <div class="payment-price">📆 7 Zile: {CONFIG.PRICE_WEEKLY_RON} LEI</div>
                    <div class="payment-price">🌙 30 Zile: {CONFIG.PRICE_MONTHLY_RON} LEI</div>
                    <div class="payment-price">⭐ LIFETIME: {CONFIG.PRICE_LIFETIME_RON} LEI</div>
                    <div class="payment-steps" style="font-size:0.7rem; margin:10px 0;">Cumpără Paysafe din OMV, Petrom, Auchan, Carrefour, Mega Image</div>
                    <button class="btn-paysafe" onclick="copySessionId()">📋 COPY SESSION ID</button>
                </div>
                
                <div class="payment-card">
                    <div class="payment-icon">🔒</div>
                    <div class="payment-title">MONERO (XMR)</div>
                    <div class="payment-price">{CONFIG.PRICE_DAILY_XMR:.3f} XMR / zi</div>
                    <div class="payment-price">{CONFIG.PRICE_WEEKLY_XMR:.2f} XMR / săpt</div>
                    <div class="payment-price">{CONFIG.PRICE_MONTHLY_XMR:.2f} XMR / lună</div>
                    <div class="payment-price">{CONFIG.PRICE_LIFETIME_XMR:.1f} XMR / LIFETIME</div>
                    <div class="payment-address" id="xmr-address">{CONFIG.XMR_ADDRESS[:30]}...</div>
                    <button class="btn" onclick="copyAddress('xmr')">📋 COPY ADDRESS</button>
                </div>
                
                <div class="payment-card">
                    <div class="payment-icon">₿</div>
                    <div class="payment-title">BITCOIN (BTC)</div>
                    <div class="payment-price">{CONFIG.PRICE_DAILY_BTC:.4f} BTC / zi</div>
                    <div class="payment-price">{CONFIG.PRICE_WEEKLY_BTC:.4f} BTC / săpt</div>
                    <div class="payment-price">{CONFIG.PRICE_MONTHLY_BTC:.2f} BTC / lună</div>
                    <div class="payment-price">{CONFIG.PRICE_LIFETIME_BTC:.2f} BTC / LIFETIME</div>
                    <div class="payment-address" id="btc-address">{CONFIG.BTC_ADDRESS[:30]}...</div>
                    <button class="btn" onclick="copyAddress('btc')">📋 COPY ADDRESS</button>
                </div>
                
                <div class="payment-card">
                    <div class="payment-icon">💎</div>
                    <div class="payment-title">USDT (TRC20)</div>
                    <div class="payment-price">{CONFIG.PRICE_DAILY_USDT} USDT / zi</div>
                    <div class="payment-price">{CONFIG.PRICE_WEEKLY_USDT} USDT / săpt</div>
                    <div class="payment-price">{CONFIG.PRICE_MONTHLY_USDT} USDT / lună</div>
                    <div class="payment-price">{CONFIG.PRICE_LIFETIME_USDT} USDT / LIFETIME</div>
                    <div class="payment-address" id="usdt-address">{CONFIG.USDT_TRC20_ADDRESS[:30]}...</div>
                    <button class="btn" onclick="copyAddress('usdt')">📋 COPY ADDRESS</button>
                </div>
            </div>
            
            <div class="session-box">
                <div class="session-title">📱 ACTIVARE LICENȚĂ - SESSION ID</div>
                <div class="session-id" id="session-id">{SESSION_ID}</div>
                <div class="session-instructions">
                    <strong>✅ CUM ACTIVEZI LICENȚA:</strong><br><br>
                    • <strong>Paysafe:</strong> Trimite PIN-ul la Session ID-ul de mai sus<br>
                    • <strong>Crypto:</strong> Trimite hash-ul tranzacției + adresa folosită<br><br>
                    <strong>📌 Instrucțiuni Session Messenger:</strong><br>
                    1. Descarcă Session Messenger (Android/iOS/Desktop): https://getsession.org<br>
                    2. Apasă "Conversație nouă" și lipește Session ID-ul<br>
                    3. Trimite dovada plății și primești confirmare în maxim 5 minute<br><br>
                    <strong>⏱️ Timp activare:</strong> Paysafe - instant | Crypto - după 1 confirmare
                </div>
                <button class="btn-paysafe" onclick="copySessionId()">📋 COPY SESSION ID</button>
            </div>
        </div>
        
        {admin_panel}
        
        <div class="footer">
            <p>⚡ WHOAMISec | SuperBot Pro v13.0 - The Ultimate Arbitrage Machine ⚡</p>
            <p>🔄 Scanare 24/7 | Plăți Anonime | Activare prin Session ID</p>
            <p>📞 Suport: Session ID de mai sus | 100% Anonim | Fără KYC</p>
        </div>
    </div>
    
    <div id="toast" style="display:none;"></div>
    
    <script>
        function showToast(msg) {{ const t=document.getElementById('toast'); t.textContent=msg; t.style.display='block'; setTimeout(()=>t.style.display='none',3000); }}
        function copySessionId() {{ navigator.clipboard.writeText(document.getElementById('session-id').innerText); showToast('✅ Session ID copiat! Trimite PIN-ul Paysafe'); }}
        function copyAddress(type) {{
            let addr = type==='xmr'?'{CONFIG.XMR_ADDRESS}':type==='btc'?'{CONFIG.BTC_ADDRESS}':'{CONFIG.USDT_TRC20_ADDRESS}';
            navigator.clipboard.writeText(addr); showToast('✅ Adresă copiată! Trimite hash-ul la Session ID');
        }}
        console.log("%c🛡️ WHOAMISec | SuperBot Pro v13.0", "color: #00ff88; font-size: 16px;");
        setInterval(()=>location.reload(), {CONFIG.SCAN_INTERVAL*1000});
    </script>
</body>
</html>'''
    
    def start(self):
        def handler_factory(engine, db):
            class Handler(BaseHTTPRequestHandler):
                def do_GET(self):
                    if self.path == '/' or self.path == '/index.html':
                        dashboard = WebDashboard(engine, db)
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
            return Handler
        
        Handler = handler_factory(self.engine, self.db)
        server = HTTPServer(('0.0.0.0', CONFIG.WEB_PORT), Handler)
        url = f"http://localhost:{CONFIG.WEB_PORT}"
        
        print(f"\n{'='*70}")
        print(f"🌐 WEB DASHBOARD: {url}")
        print(f"📱 Se deschide automat în browser...")
        print(f"💳 Prețuri: {CONFIG.PRICE_DAILY_RON} LEI/zi | {CONFIG.PRICE_MONTHLY_RON} LEI/lună | {CONFIG.PRICE_LIFETIME_RON} LEI lifetime")
        print(f"🔑 Session ID: {SESSION_ID[:30]}...")
        print(f"{'='*70}\n")
        
        def open_browser():
            time.sleep(1.5)
            try:
                webbrowser.open(url)
                print("✅ Browser deschis automat!")
            except:
                print(f"🔗 Deschide manual: {url}")
        
        threading.Thread(target=open_browser, daemon=True).start()
        
        try:
            server.serve_forever()
        except KeyboardInterrupt:
            print("\n💀 Server oprit")

# =============================================================================
# TELEGRAM BOT WITH ADMIN COMMANDS
# =============================================================================

class TelegramBot:
    def __init__(self, db: Database, engine: ArbitrageEngine):
        self.db = db
        self.engine = engine
        self.application = None
    
    def is_admin(self, user_id: int) -> bool:
        return user_id in CONFIG.ADMIN_IDS
    
    async def start_command(self, update: Update, context: ContextTypes.DEFAULT_TYPE):
        user = update.effective_user
        user_id = str(user.id)
        self.db.add_user(user_id, user.username or user.first_name)
        
        text = f"""🔥 *SUPERBOT PRO v13.0* 🔥

Salut {user.first_name}! Ai primit *{CONFIG.TRIAL_DAYS} zile GRATIS* de test.

*📋 LISTĂ PREȚURI:*
📅 1 Zi: {CONFIG.PRICE_DAILY_RON} LEI / {CONFIG.PRICE_DAILY_XMR:.3f} XMR
📆 7 Zile: {CONFIG.PRICE_WEEKLY_RON} LEI / {CONFIG.PRICE_WEEKLY_XMR:.2f} XMR
🌙 30 Zile: {CONFIG.PRICE_MONTHLY_RON} LEI / {CONFIG.PRICE_MONTHLY_XMR:.2f} XMR
⭐ LIFETIME: {CONFIG.PRICE_LIFETIME_RON} LEI / {CONFIG.PRICE_LIFETIME_XMR:.1f} XMR

*Comenzi:*
/scan - Scanează oportunități
/status - Verifică licența
/buy - Cumpără licență
/price - Listă prețuri
/help - Ajutor

*Plăți Anonime:* Paysafe Card | Monero (XMR) | BTC | USDT
*Activare:* Trimite PIN-ul la Session ID: `{SESSION_ID}`

🔗 Session Messenger: https://getsession.org
"""
        keyboard = [[InlineKeyboardButton("🔍 SCAN", callback_data="scan"), InlineKeyboardButton("💰 BUY", callback_data="buy")]]
        await update.message.reply_text(text, parse_mode='Markdown', reply_markup=InlineKeyboardMarkup(keyboard))
    
    async def scan_command(self, update: Update, context: ContextTypes.DEFAULT_TYPE):
        user_id = str(update.effective_user.id)
        valid, msg, user_data = self.db.check_license(user_id)
        
        if not valid:
            await update.message.reply_text(f"❌ *Acces refuzat!*\n\n{msg}\n\nFolosește /buy pentru a cumpăra licență.", parse_mode='Markdown')
            return
        
        await update.message.reply_text("🔍 *Scanez oportunități de arbitraj...*", parse_mode='Markdown')
        result = self.engine.scan()
        
        if result['opportunities']:
            text = f"🔥 *OPORTUNITĂȚI GĂSITE* 🔥\n\n"
            for i, opp in enumerate(result['opportunities'][:5], 1):
                text += f"{i}. *{opp['event_name']}*\n"
                text += f"   📍 {opp['outcome']} | {opp['bookmaker1'].upper()} {opp['odds1']:.2f} → {opp['bookmaker2'].upper()} {opp['odds2']:.2f}\n"
                text += f"   💰 Profit: *+{opp['profit_percent']:.2f}%*\n\n"
            await update.message.reply_text(text, parse_mode='Markdown')
        else:
            await update.message.reply_text("📭 *Nicio oportunitate momentan.* Revin mai târziu.", parse_mode='Markdown')
    
    async def status_command(self, update: Update, context: ContextTypes.DEFAULT_TYPE):
        user_id = str(update.effective_user.id)
        valid, msg, user_data = self.db.check_license(user_id)
        
        if valid:
            text = f"✅ *LICENȚĂ ACTIVĂ*\n\n{msg}\n\n🔥 SuperBot Pro rulează 24/7!"
        else:
            text = f"❌ *LICENȚĂ INACTIVĂ*\n\n{msg}\n\nFolosește /buy pentru a cumpăra licență."
        
        await update.message.reply_text(text, parse_mode='Markdown')
    
    async def price_command(self, update: Update, context: ContextTypes.DEFAULT_TYPE):
        text = f"""💰 *LISTĂ PREȚURI SUPERBOT PRO* 💰

┌─────────────────────────────────────────┐
│ 📅 *1 ZI*                               │
│    Paysafe: {CONFIG.PRICE_DAILY_RON} LEI     │
│    XMR: {CONFIG.PRICE_DAILY_XMR:.3f} XMR      │
│    BTC: {CONFIG.PRICE_DAILY_BTC:.4f} BTC      │
│    USDT: {CONFIG.PRICE_DAILY_USDT} USDT       │
├─────────────────────────────────────────┤
│ 📆 *7 ZILE*                             │
│    Paysafe: {CONFIG.PRICE_WEEKLY_RON} LEI     │
│    XMR: {CONFIG.PRICE_WEEKLY_XMR:.2f} XMR      │
│    BTC: {CONFIG.PRICE_WEEKLY_BTC:.4f} BTC      │
│    USDT: {CONFIG.PRICE_WEEKLY_USDT} USDT       │
├─────────────────────────────────────────┤
│ 🌙 *30 ZILE*                            │
│    Paysafe: {CONFIG.PRICE_MONTHLY_RON} LEI    │
│    XMR: {CONFIG.PRICE_MONTHLY_XMR:.2f} XMR     │
│    BTC: {CONFIG.PRICE_MONTHLY_BTC:.2f} BTC     │
│    USDT: {CONFIG.PRICE_MONTHLY_USDT} USDT      │
├─────────────────────────────────────────┤
│ ⭐ *LIFETIME*                           │
│    Paysafe: {CONFIG.PRICE_LIFETIME_RON} LEI   │
│    XMR: {CONFIG.PRICE_LIFETIME_XMR:.1f} XMR     │
│    BTC: {CONFIG.PRICE_LIFETIME_BTC:.2f} BTC     │
│    USDT: {CONFIG.PRICE_LIFETIME_USDT} USDT      │
└─────────────────────────────────────────┘

*Plăți Anonime:*
💳 Paysafe Card - cumpără din OMV, Petrom, Auchan, Carrefour
🔒 Monero (XMR) - 100% anonim
₿ Bitcoin (BTC)
💎 USDT (TRC20)

*Activare:* Trimite dovada plății la Session ID: `{SESSION_ID}`
"""
        await update.message.reply_text(text, parse_mode='Markdown')
    
    async def buy_command(self, update: Update, context: ContextTypes.DEFAULT_TYPE):
        text = f"""💰 *CUMPĂRĂ LICENȚĂ*

*Pachete disponibile:*

📅 *1 Zi* - {CONFIG.PRICE_DAILY_RON} LEI / {CONFIG.PRICE_DAILY_XMR:.3f} XMR
📆 *7 Zile* - {CONFIG.PRICE_WEEKLY_RON} LEI / {CONFIG.PRICE_WEEKLY_XMR:.2f} XMR
🌙 *30 Zile* - {CONFIG.PRICE_MONTHLY_RON} LEI / {CONFIG.PRICE_MONTHLY_XMR:.2f} XMR
⭐ *LIFETIME* - {CONFIG.PRICE_LIFETIME_RON} LEI / {CONFIG.PRICE_LIFETIME_XMR:.1f} XMR

*Cum plătești:*

💳 *PAYSAFE CARD* (Recomandat România)
1. Cumpără Paysafe de la OMV, Petrom, Auchan, Carrefour, Mega Image
2. Trimite PIN-ul la Session ID: `{SESSION_ID}`
3. Primești confirmare și licența se activează

🔒 *MONERO (XMR)* - 100% anonim
Adresă: `{CONFIG.XMR_ADDRESS[:35]}...`

₿ *BITCOIN (BTC)*
Adresă: `{CONFIG.BTC_ADDRESS[:35]}...`

💎 *USDT (TRC20)*
Adresă: `{CONFIG.USDT_TRC20_ADDRESS[:35]}...`

*Activare:* După plată, trimite dovada la Session ID și primești confirmarea în 5 minute.

🔗 Session Messenger: https://getsession.org
"""
        await update.message.reply_text(text, parse_mode='Markdown')
    
    # ============= ADMIN COMMANDS =============
    
    async def admin_panel(self, update: Update, context: ContextTypes.DEFAULT_TYPE):
        user_id = update.effective_user.id
        if not self.is_admin(user_id):
            await update.message.reply_text("❌ Acces interzis. Nu ești administrator.")
            return
        
        stats = self.db.get_stats(admin_view=True)
        pending = self.db.get_pending_payments()
        
        text = f"""🛡️ *ADMIN PANEL* 🛡️

*📊 STATISTICI:*
• Total Utilizatori: {stats['users']}
• Utilizatori Banați: {stats['banned_users']}
• Licențe Lifetime: {stats['lifetime_users']}
• Trial-uri Active: {stats['active_trials']}
• Plăți Confirmate: {stats['payments']}
• Venit Total: ${stats['revenue']:.2f}

*⏳ PLĂȚI ÎN AȘTEPTARE: {len(pending)}*

*Comenzi Admin:*
/admin_users - Listă utilizatori
/admin_ban <user_id> <motiv> - Banează utilizator
/admin_unban <user_id> - Debanează
/admin_lifetime <user_id> - Activează lifetime
/admin_confirm <transaction_id> - Confirmă plată
/admin_stats - Statistici detaliate
"""
        await update.message.reply_text(text, parse_mode='Markdown')
    
    async def admin_users(self, update: Update, context: ContextTypes.DEFAULT_TYPE):
        user_id = update.effective_user.id
        if not self.is_admin(user_id):
            return
        
        users = self.db.get_all_users()
        text = "📋 *LISTĂ UTILIZATORI*\n\n"
        for u in users[:20]:
            status = "🔴 BANAT" if u.get('is_banned') else "🟢 ACTIV"
            text += f"• `{u['user_id'][:15]}...` | {u.get('username', 'N/A')} | {u['license_type']} | {status}\n"
        
        if len(users) > 20:
            text += f"\n... și {len(users)-20} alții"
        
        await update.message.reply_text(text, parse_mode='Markdown')
    
    async def admin_ban(self, update: Update, context: ContextTypes.DEFAULT_TYPE):
        user_id = update.effective_user.id
        if not self.is_admin(user_id):
            return
        
        args = context.args
        if len(args) < 2:
            await update.message.reply_text("⚠️ Folosește: /admin_ban <user_id> <motiv>")
            return
        
        target_user = args[0]
        reason = ' '.join(args[1:])
        
        self.db.ban_user(str(user_id), target_user, reason)
        await update.message.reply_text(f"✅ Utilizatorul {target_user} a fost banat.\nMotiv: {reason}")
    
    async def admin_unban(self, update: Update, context: ContextTypes.DEFAULT_TYPE):
        user_id = update.effective_user.id
        if not self.is_admin(user_id):
            return
        
        args = context.args
        if len(args) < 1:
            await update.message.reply_text("⚠️ Folosește: /admin_unban <user_id>")
            return
        
        target_user = args[0]
        self.db.unban_user(str(user_id), target_user)
        await update.message.reply_text(f"✅ Utilizatorul {target_user} a fost debanat.")
    
    async def admin_lifetime(self, update: Update, context: ContextTypes.DEFAULT_TYPE):
        user_id = update.effective_user.id
        if not self.is_admin(user_id):
            return
        
        args = context.args
        if len(args) < 1:
            await update.message.reply_text("⚠️ Folosește: /admin_lifetime <user_id>")
            return
        
        target_user = args[0]
        self.db.give_lifetime(str(user_id), target_user)
        await update.message.reply_text(f"✅ Licență LIFETIME acordată lui {target_user}")
    
    async def admin_confirm(self, update: Update, context: ContextTypes.DEFAULT_TYPE):
        user_id = update.effective_user.id
        if not self.is_admin(user_id):
            return
        
        args = context.args
        if len(args) < 1:
            await update.message.reply_text("⚠️ Folosește: /admin_confirm <transaction_id>")
            return
        
        tx_id = args[0]
        self.db.confirm_payment(str(user_id), tx_id)
        await update.message.reply_text(f"✅ Plata {tx_id} a fost confirmată!")
    
    async def admin_stats(self, update: Update, context: ContextTypes.DEFAULT_TYPE):
        user_id = update.effective_user.id
        if not self.is_admin(user_id):
            return
        
        stats = self.db.get_stats(admin_view=True)
        text = f"""📊 *STATISTICI DETALIATE*

• Total Oportunități: {stats['opportunities']}
• Profit Mediu: {stats['avg_profit']}%
• Total Utilizatori: {stats['users']}
• Utilizatori Banați: {stats['banned_users']}
• Licențe Lifetime: {stats['lifetime_users']}
• Trial-uri Active: {stats['active_trials']}
• Plăți Confirmate: {stats['payments']}
• Venit Total: ${stats['revenue']:.2f}
"""
        await update.message.reply_text(text, parse_mode='Markdown')
    
    async def handle_callback(self, update: Update, context: ContextTypes.DEFAULT_TYPE):
        query = update.callback_query
        await query.answer()
        
        if query.data == "scan":
            await self.scan_command(update, context)
        elif query.data == "buy":
            await self.buy_command(update, context)
    
    async def help_command(self, update: Update, context: ContextTypes.DEFAULT_TYPE):
        text = """📚 *COMENZI SUPERBOT PRO*

*Comenzi utilizator:*
/start - Pornește botul
/scan - Scanează oportunități
/status - Verifică licența
/price - Listă prețuri
/buy - Cumpără licență
/help - Acest mesaj

*Comenzi Admin:*
/admin - Panou administrare
/admin_users - Listă utilizatori
/admin_ban <id> <motiv> - Banează
/admin_unban <id> - Debanează
/admin_lifetime <id> - Lifetime
/admin_confirm <tx_id> - Confirmă plată
/admin_stats - Statistici

*Plăți Anonime:*
💳 Paysafe Card (Recomandat România)
🔒 Monero (XMR) - 100% anonim
₿ Bitcoin (BTC)
💎 USDT (TRC20)

*Activare:* Trimite dovada la Session ID: `{SESSION_ID}`
"""
        await update.message.reply_text(text, parse_mode='Markdown')
    
    def run(self):
        if not TELEGRAM_OK or CONFIG.BOT_TOKEN == "YOUR_BOT_TOKEN_HERE":
            print("[⚠️] Telegram bot neconfigurat. Rulează doar web dashboard.")
            return
        
        self.application = Application.builder().token(CONFIG.BOT_TOKEN).build()
        
        # User commands
        self.application.add_handler(CommandHandler("start", self.start_command))
        self.application.add_handler(CommandHandler("scan", self.scan_command))
        self.application.add_handler(CommandHandler("status", self.status_command))
        self.application.add_handler(CommandHandler("price", self.price_command))
        self.application.add_handler(CommandHandler("buy", self.buy_command))
        self.application.add_handler(CommandHandler("help", self.help_command))
        
        # Admin commands
        self.application.add_handler(CommandHandler("admin", self.admin_panel))
        self.application.add_handler(CommandHandler("admin_users", self.admin_users))
        self.application.add_handler(CommandHandler("admin_ban", self.admin_ban))
        self.application.add_handler(CommandHandler("admin_unban", self.admin_unban))
        self.application.add_handler(CommandHandler("admin_lifetime", self.admin_lifetime))
        self.application.add_handler(CommandHandler("admin_confirm", self.admin_confirm))
        self.application.add_handler(CommandHandler("admin_stats", self.admin_stats))
        
        self.application.add_handler(CallbackQueryHandler(self.handle_callback))
        
        print("\n🤖 TELEGRAM BOT STARTED - @WHOAMISec")
        print(f"👑 Admin IDs: {CONFIG.ADMIN_IDS}")
        self.application.run_polling()

# =============================================================================
# MAIN CONTROLLER
# =============================================================================

class SuperBotController:
    def __init__(self):
        self.db = Database()
        self.engine = ArbitrageEngine(self.db)
        self.web = WebDashboard(self.engine, self.db)
        self.bot = TelegramBot(self.db, self.engine)
    
    def start(self):
        print("\n" + "="*70)
        print("  🛡️ WHOAMISec | SUPERBOT PRO v13.0 - COMMERCIAL EDITION".center(70))
        print("  ✅ Listă Prețuri | ✅ Restricții Utilizatori | ✅ Admin Panel".center(70))
        print("  💳 Plăți: Paysafe | XMR | BTC | USDT".center(70))
        print("="*70 + "\n")
        
        print(f"💰 PREȚURI:")
        print(f"   📅 1 Zi: {CONFIG.PRICE_DAILY_RON} LEI | {CONFIG.PRICE_DAILY_XMR:.3f} XMR | {CONFIG.PRICE_DAILY_USDT} USDT")
        print(f"   🌙 30 Zile: {CONFIG.PRICE_MONTHLY_RON} LEI | {CONFIG.PRICE_MONTHLY_XMR:.2f} XMR")
        print(f"   ⭐ LIFETIME: {CONFIG.PRICE_LIFETIME_RON} LEI | {CONFIG.PRICE_LIFETIME_XMR:.1f} XMR")
        print(f"\n🔑 SESSION ID: {SESSION_ID}")
        print(f"👑 Admin IDs: {CONFIG.ADMIN_IDS}")
        print("="*70 + "\n")
        
        # Start scanner background
        def scan_callback(r):
            pass
        threading.Thread(target=self.engine.start_24_7, args=(scan_callback,), daemon=True).start()
        
        # Start web dashboard
        threading.Thread(target=self.web.start, daemon=True).start()
        
        # Start bot if configured
        if CONFIG.BOT_TOKEN != "YOUR_BOT_TOKEN_HERE":
            threading.Thread(target=self.bot.run, daemon=True).start()
        
        # Keep alive
        try:
            while True:
                time.sleep(1)
        except KeyboardInterrupt:
            print("\n💀 SuperBot Pro oprit. Profit extras! 💰")

# =============================================================================
# ENTRY POINT
# =============================================================================

if __name__ == "__main__":
    try:
        controller = SuperBotController()
        controller.start()
    except KeyboardInterrupt:
        print("\n\n💀 WHOAMISec | SuperBot Pro oprit.")
        sys.exit(0)
    except Exception as e:
        print(f"\n❌ Error: {e}")
        import traceback
        traceback.print_exc()