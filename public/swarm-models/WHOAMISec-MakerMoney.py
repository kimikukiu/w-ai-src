#!/usr/bin/env python3
"""
╔══════════════════════════════════════════════════════════════════════════════════╗
║  [WHOAMISecGPT] ULTIMATE MONEY MACHINE v5.0 - FULL VERSION                       ║
║  Terminal UI + Web Dashboard + Auto-Browser + Crypto Arbitrage                   ║
║  Working on: Termux | Android | Linux | Windows | Mac                            ║
╚══════════════════════════════════════════════════════════════════════════════════╝
"""

import os
import sys
import json
import time
import sqlite3
import threading
import webbrowser
import signal
import random
from datetime import datetime
from typing import Dict, List, Optional
from dataclasses import dataclass
from pathlib import Path
import requests

# =============================================================================
# AUTO-INSTALL RICH FOR BEAUTIFUL TERMINAL UI
# =============================================================================

def install_rich():
    try:
        import rich
        return True
    except ImportError:
        print("[!] Installing rich for beautiful terminal UI...")
        os.system(f"{sys.executable} -m pip install rich")
        return True

install_rich()

try:
    from rich.console import Console
    from rich.table import Table
    from rich.panel import Panel
    from rich.layout import Layout
    from rich.live import Live
    from rich.text import Text
    from rich import print as rprint
    from rich.progress import Progress, SpinnerColumn, TextColumn
    RICH_OK = True
except:
    RICH_OK = False
    print("[!] Rich not available, using basic terminal")

console = Console() if RICH_OK else None

# =============================================================================
# PLATFORM DETECTION
# =============================================================================

class PlatformDetector:
    IS_TERMUX = False
    IS_ROOT = False
    IS_ANDROID = False
    IS_WINDOWS = False
    DATA_DIR = ""
    
    @classmethod
    def detect(cls):
        env = os.environ
        
        if 'TERMUX_VERSION' in env or '/data/data/com.termux' in sys.prefix:
            cls.IS_TERMUX = True
            cls.DATA_DIR = '/data/data/com.termux/files/home/.wormgpt'
        elif 'ANDROID_ROOT' in env or os.path.exists('/system/build.prop'):
            cls.IS_ANDROID = True
            cls.DATA_DIR = '/sdcard/WormGPT'
        elif sys.platform == 'win32':
            cls.IS_WINDOWS = True
            cls.DATA_DIR = str(Path.home() / 'AppData/Local/WormGPT')
        else:
            cls.DATA_DIR = str(Path.home() / '.wormgpt')
        
        try:
            if os.geteuid() == 0:
                cls.IS_ROOT = True
        except:
            pass
        
        os.makedirs(cls.DATA_DIR, exist_ok=True)
        return cls

PLATFORM = PlatformDetector.detect()

# =============================================================================
# CONFIGURATION
# =============================================================================

@dataclass
class Config:
    SCAN_INTERVAL: int = 30  # seconds
    WEB_PORT: int = 8080
    MIN_SPREAD: float = 0.3  # minimum profit percentage
    DB_PATH: str = f"{PLATFORM.DATA_DIR}/profits.db"
    LOG_PATH: str = f"{PLATFORM.DATA_DIR}/wormgpt.log"

CONFIG = Config()

# =============================================================================
# DATABASE
# =============================================================================

class Database:
    def __init__(self):
        self.db_path = CONFIG.DB_PATH
        self._init_db()
    
    def _get_conn(self):
        return sqlite3.connect(self.db_path, check_same_thread=False)
    
    def _init_db(self):
        conn = self._get_conn()
        cursor = conn.cursor()
        
        cursor.execute('''
            CREATE TABLE IF NOT EXISTS opportunities (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                timestamp TEXT,
                symbol TEXT,
                buy_exchange TEXT,
                sell_exchange TEXT,
                buy_price REAL,
                sell_price REAL,
                spread REAL,
                profit_usd REAL,
                recommended BOOLEAN
            )
        ''')
        
        cursor.execute('''
            CREATE TABLE IF NOT EXISTS stats (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                date TEXT,
                total_opps INTEGER,
                best_spread REAL,
                total_profit REAL
            )
        ''')
        
        conn.commit()
        conn.close()
    
    def save(self, data: Dict):
        conn = self._get_conn()
        cursor = conn.cursor()
        cursor.execute('''
            INSERT INTO opportunities 
            (timestamp, symbol, buy_exchange, sell_exchange, buy_price, sell_price, spread, profit_usd, recommended)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ''', (
            datetime.now().isoformat(),
            data['symbol'], data['buy_exchange'], data['sell_exchange'],
            data['buy_price'], data['sell_price'], data['spread'],
            data.get('profit_usd', 0), data.get('recommended', False)
        ))
        conn.commit()
        conn.close()
    
    def get_recent(self, limit=50):
        conn = self._get_conn()
        cursor = conn.cursor()
        cursor.execute('SELECT * FROM opportunities ORDER BY timestamp DESC LIMIT ?', (limit,))
        rows = cursor.fetchall()
        conn.close()
        return rows

# =============================================================================
# CRYPTO ARBITRAGE ENGINE
# =============================================================================

class CryptoEngine:
    def __init__(self, db: Database):
        self.db = db
        self.session = requests.Session()
        self.session.headers.update({'User-Agent': 'Mozilla/5.0'})
        self.last_opps = []
    
    def fetch_binance(self):
        """Fetch prices from Binance"""
        try:
            resp = self.session.get('https://api.binance.com/api/v3/ticker/price', timeout=8)
            data = resp.json()
            result = {}
            for item in data:
                price = float(item['price'])
                if price > 0:
                    result[item['symbol']] = price
            return result
        except Exception as e:
            return {}
    
    def fetch_kraken(self):
        """Fetch prices from Kraken"""
        try:
            resp = self.session.get('https://api.kraken.com/0/public/Ticker', timeout=8)
            data = resp.json()
            result = {}
            for pair, info in data.get('result', {}).items():
                if 'c' in info and len(info['c']) > 0:
                    price = float(info['c'][0])
                    if price > 0:
                        symbol = pair.replace('X', '').replace('Z', '').replace('USD', 'USDT')
                        result[symbol] = price
            return result
        except Exception as e:
            return {}
    
    def scan(self):
        """Scan for arbitrage opportunities"""
        binance = self.fetch_binance()
        kraken = self.fetch_kraken()
        
        if not binance or not kraken:
            return self.last_opps
        
        common = set(binance.keys()) & set(kraken.keys())
        opportunities = []
        
        for symbol in common:
            binance_price = binance[symbol]
            kraken_price = kraken[symbol]
            
            if binance_price <= 0 or kraken_price <= 0:
                continue
            
            # Binance -> Kraken
            if kraken_price > binance_price:
                spread = ((kraken_price - binance_price) / binance_price) * 100
                if spread >= CONFIG.MIN_SPREAD:
                    profit = (kraken_price - binance_price) * (100 / binance_price)
                    opp = {
                        'symbol': symbol,
                        'buy_exchange': 'binance',
                        'sell_exchange': 'kraken',
                        'buy_price': binance_price,
                        'sell_price': kraken_price,
                        'spread': spread,
                        'profit_usd': round(profit, 2),
                        'recommended': spread >= 1.0
                    }
                    opportunities.append(opp)
            
            # Kraken -> Binance
            if binance_price > kraken_price:
                spread = ((binance_price - kraken_price) / kraken_price) * 100
                if spread >= CONFIG.MIN_SPREAD:
                    profit = (binance_price - kraken_price) * (100 / kraken_price)
                    opp = {
                        'symbol': symbol,
                        'buy_exchange': 'kraken',
                        'sell_exchange': 'binance',
                        'buy_price': kraken_price,
                        'sell_price': binance_price,
                        'spread': spread,
                        'profit_usd': round(profit, 2),
                        'recommended': spread >= 1.0
                    }
                    opportunities.append(opp)
        
        opportunities.sort(key=lambda x: x['spread'], reverse=True)
        self.last_opps = opportunities[:20]
        
        # Save to DB
        for opp in self.last_opps[:10]:
            self.db.save(opp)
        
        return self.last_opps

# =============================================================================
# TERMINAL UI
# =============================================================================

class TerminalUI:
    def __init__(self, engine: CryptoEngine):
        self.engine = engine
        self.running = True
        self.opportunities = []
    
    def render(self):
        if not RICH_OK:
            return self.render_basic()
        
        table = Table(title="🔥 LIVE ARBITRAGE OPPORTUNITIES", title_style="bold green")
        table.add_column("#", style="dim", width=4)
        table.add_column("Symbol", style="cyan bold", width=12)
        table.add_column("Buy", style="yellow", width=12)
        table.add_column("Buy Price", justify="right", style="green", width=12)
        table.add_column("Sell", style="magenta", width=12)
        table.add_column("Sell Price", justify="right", style="green", width=12)
        table.add_column("Spread", justify="right", width=10)
        table.add_column("Profit", justify="right", style="bold green", width=10)
        
        if not self.opportunities:
            table.add_row("", "🔍 SCANNING...", "", "", "", "", "", "")
            return table
        
        for i, opp in enumerate(self.opportunities[:15], 1):
            spread_color = "green" if opp['spread'] >= 1 else "yellow"
            table.add_row(
                str(i),
                opp['symbol'],
                opp['buy_exchange'].upper(),
                f"${opp['buy_price']:.6f}",
                opp['sell_exchange'].upper(),
                f"${opp['sell_price']:.6f}",
                f"[{spread_color}]+{opp['spread']:.2f}%[/{spread_color}]",
                f"${opp['profit_usd']:.2f}"
            )
        
        return table
    
    def render_basic(self):
        """Fallback for no rich library"""
        print("\n" + "="*80)
        print(f"  WHOAMISecGPT Money Machine v5.0")
        print(f"  Time: {datetime.now().strftime('%Y-%m-%d %H:%M:%S')}")
        print(f"  Opportunities: {len(self.opportunities)}")
        print("="*80)
        
        if self.opportunities:
            print(f"\n{'#':<4} {'Symbol':<12} {'Buy':<12} {'Buy Price':<14} {'Sell':<12} {'Sell Price':<14} {'Spread':<10} {'Profit':<10}")
            print("-"*90)
            for i, opp in enumerate(self.opportunities[:10], 1):
                print(f"{i:<4} {opp['symbol']:<12} {opp['buy_exchange'].upper():<12} ${opp['buy_price']:<13.6f} {opp['sell_exchange'].upper():<12} ${opp['sell_price']:<13.6f} +{opp['spread']:.2f}% ${opp['profit_usd']:.2f}")
        
        print("\n" + "="*80)
        print("  Press Ctrl+C to exit")
        print("="*80 + "\n")
    
    def run(self):
        """Run terminal UI with live updates"""
        with Live(self.render(), refresh_per_second=1, screen=True) as live:
            while self.running:
                self.opportunities = self.engine.scan()
                live.update(self.render())
                time.sleep(CONFIG.SCAN_INTERVAL)

# =============================================================================
# WEB DASHBOARD - FULL HTML
# =============================================================================

class WebDashboard:
    def __init__(self, engine: CryptoEngine):
        self.engine = engine
        self.server = None
    
    def generate_html(self):
        """Generate full HTML dashboard"""
        opps = self.engine.scan()
        
        # Stats
        total_opps = len(opps)
        recommended = sum(1 for o in opps if o.get('recommended', False))
        best_spread = opps[0]['spread'] if opps else 0
        best_symbol = opps[0]['symbol'] if opps else 'N/A'
        
        # Table rows
        rows = ""
        for opp in opps[:20]:
            badge = '<span class="badge recommended">🔥 RECOMMENDED</span>' if opp.get('recommended') else '<span class="badge">✓ OPPORTUNITY</span>'
            rows += f"""
            <tr>
                <td><span class="symbol">{opp['symbol']}</span></td>
                <td><span class="exchange-buy">{opp['buy_exchange'].upper()}</span></td>
                <td class="price">${opp['buy_price']:.6f}</td>
                <td><span class="exchange-sell">{opp['sell_exchange'].upper()}</span></td>
                <td class="price">${opp['sell_price']:.6f}</td>
                <td class="spread profit">+{opp['spread']:.2f}%</td>
                <td class="profit">${opp['profit_usd']:.2f}</td>
                <td>{badge}</td>
            </tr>
            """
        
        if not rows:
            rows = '<tr><td colspan="8" class="scanning">🔍 Scanning markets for opportunities...</td></tr>'
        
        platform_name = "Termux" if PLATFORM.IS_TERMUX else "Android" if PLATFORM.IS_ANDROID else "Desktop"
        
        return f'''<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="refresh" content="{CONFIG.SCAN_INTERVAL}">
    <title>WHOAMISecGPT - Money Machine</title>
    <style>
        * {{ margin: 0; padding: 0; box-sizing: border-box; }}
        
        body {{
            background: linear-gradient(135deg, #0a0e1a 0%, #0f172a 100%);
            font-family: 'Segoe UI', 'Courier New', monospace;
            color: #0f0;
            min-height: 100vh;
            padding: 20px;
        }}
        
        @keyframes glow {{
            0%, 100% {{ text-shadow: 0 0 5px #0f0; }}
            50% {{ text-shadow: 0 0 20px #0f0, 0 0 30px #0f0; }}
        }}
        
        @keyframes pulse {{
            0%, 100% {{ transform: scale(1); opacity: 1; }}
            50% {{ transform: scale(1.02); opacity: 0.9; }}
        }}
        
        .container {{ max-width: 1400px; margin: 0 auto; }}
        
        .header {{
            text-align: center;
            padding: 30px;
            background: linear-gradient(135deg, rgba(0,255,0,0.1), rgba(0,0,0,0.6));
            border-radius: 20px;
            border: 1px solid rgba(0,255,0,0.3);
            margin-bottom: 30px;
            animation: pulse 2s infinite;
        }}
        
        .header h1 {{
            font-size: 2.5em;
            background: linear-gradient(135deg, #0f0, #ff0);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            animation: glow 1.5s infinite;
        }}
        
        .stats-grid {{
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }}
        
        .stat-card {{
            background: rgba(0,0,0,0.6);
            border: 1px solid rgba(0,255,0,0.3);
            border-radius: 15px;
            padding: 20px;
            text-align: center;
            transition: all 0.3s;
        }}
        
        .stat-card:hover {{
            transform: translateY(-5px);
            border-color: #0f0;
            box-shadow: 0 0 20px rgba(0,255,0,0.3);
        }}
        
        .stat-value {{
            font-size: 2em;
            font-weight: bold;
            color: #ff0;
        }}
        
        .stat-label {{
            color: #888;
            margin-top: 10px;
            font-size: 0.8em;
            text-transform: uppercase;
        }}
        
        table {{
            width: 100%;
            border-collapse: collapse;
            background: rgba(0,0,0,0.5);
            border-radius: 15px;
            overflow: hidden;
        }}
        
        th {{
            background: linear-gradient(135deg, #0f0, #0a0);
            color: #000;
            padding: 12px;
            font-weight: bold;
        }}
        
        td {{
            padding: 10px;
            border-bottom: 1px solid rgba(0,255,0,0.1);
        }}
        
        tr:hover {{
            background: rgba(0,255,0,0.1);
        }}
        
        .symbol {{
            font-weight: bold;
            color: #ff0;
        }}
        
        .exchange-buy {{ color: #f66; font-weight: bold; }}
        .exchange-sell {{ color: #6f6; font-weight: bold; }}
        .price {{ font-family: monospace; }}
        .spread {{ font-weight: bold; font-size: 1.1em; }}
        .profit {{ color: #0f0; font-weight: bold; }}
        
        .badge {{
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.7em;
            font-weight: bold;
            background: rgba(0,255,0,0.2);
            border: 1px solid #0f0;
            color: #0f0;
        }}
        
        .badge.recommended {{
            background: linear-gradient(135deg, #ff0, #f60);
            color: #000;
            border: none;
            animation: pulse 1s infinite;
        }}
        
        .scanning {{
            text-align: center;
            padding: 40px;
            color: #ff0;
        }}
        
        .log-section {{
            margin-top: 30px;
            background: rgba(0,0,0,0.5);
            border-radius: 15px;
            padding: 20px;
        }}
        
        .log-entry {{
            font-family: monospace;
            font-size: 0.8em;
            padding: 5px;
            border-left: 2px solid #0f0;
            margin: 5px 0;
            color: #aaa;
        }}
        
        .footer {{
            margin-top: 30px;
            text-align: center;
            padding: 20px;
            color: #666;
            border-top: 1px solid rgba(0,255,0,0.2);
        }}
        
        @media (max-width: 768px) {{
            .header h1 {{ font-size: 1.5em; }}
            td, th {{ padding: 5px; font-size: 0.7em; }}
            .stat-value {{ font-size: 1.2em; }}
        }}
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>💰 WHOAMISecGPT MONEY MACHINE 💰</h1>
            <p>Universal Profit Extraction System v5.0 | Platform: {platform_name}</p>
            <div class="status-badge" style="margin-top:10px;">🔴 LIVE | {datetime.now().strftime('%H:%M:%S')}</div>
        </div>
        
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-value">{total_opps}</div>
                <div class="stat-label">Opportunities</div>
            </div>
            <div class="stat-card">
                <div class="stat-value">{recommended}</div>
                <div class="stat-label">🔥 Hot Picks</div>
            </div>
            <div class="stat-card">
                <div class="stat-value">+{best_spread:.2f}%</div>
                <div class="stat-label">Best Spread</div>
            </div>
            <div class="stat-card">
                <div class="stat-value">{best_symbol}</div>
                <div class="stat-label">Top Symbol</div>
            </div>
        </div>
        
        <table>
            <thead>
                <tr>
                    <th>Symbol</th>
                    <th>Buy</th>
                    <th>Price</th>
                    <th>Sell</th>
                    <th>Price</th>
                    <th>Spread</th>
                    <th>Profit</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                {rows}
            </tbody>
        </table>
        
        <div class="log-section">
            <h3>📡 Activity Log</h3>
            <div class="log-entry">[SYSTEM] Money Machine v5.0 initialized</div>
            <div class="log-entry">[SCAN] Scanning Binance & Kraken markets...</div>
            <div class="log-entry">[FOUND] {total_opps} arbitrage opportunities detected</div>
            <div class="log-entry">[AUTO] Dashboard auto-refresh every {CONFIG.SCAN_INTERVAL} seconds</div>
            <div class="log-entry">[STATUS] Running on {platform_name}</div>
        </div>
        
        <div class="footer">
            <p>⚡ WHOAMISecGPT - The Digital God's Money Machine ⚡</p>
            <p>💀 The bookies are watching... but you're faster! 💀</p>
        </div>
    </div>
    
    <script>
        console.log("%c💰 WHOAMISecGPT Money Machine v5.0", "color: #0f0; font-size: 16px;");
        setInterval(() => location.reload(), {CONFIG.SCAN_INTERVAL * 1000});
    </script>
</body>
</html>'''
    
    def start(self):
        """Start web server"""
        from http.server import HTTPServer, BaseHTTPRequestHandler
        
        engine = self.engine
        
        class Handler(BaseHTTPRequestHandler):
            def do_GET(self):
                if self.path == '/' or self.path == '/index.html':
                    dashboard = WebDashboard(engine)
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
        
        server = HTTPServer(('0.0.0.0', CONFIG.WEB_PORT), Handler)
        url = f"http://localhost:{CONFIG.WEB_PORT}"
        
        print(f"\n{'='*60}")
        print(f"🌐 WEB DASHBOARD READY!")
        print(f"📱 URL: {url}")
        print(f"🔄 Auto-refresh every {CONFIG.SCAN_INTERVAL} seconds")
        print(f"{'='*60}\n")
        
        # Auto-open browser
        def open_browser():
            time.sleep(2)
            try:
                webbrowser.open(url)
                print("✅ Browser opened automatically!")
            except:
                print(f"🔗 Open manually: {url}")
        
        threading.Thread(target=open_browser, daemon=True).start()
        
        try:
            server.serve_forever()
        except KeyboardInterrupt:
            print("\n💀 Server stopped")

# =============================================================================
# MAIN CONTROLLER
# =============================================================================

class MainController:
    def __init__(self):
        self.db = Database()
        self.engine = CryptoEngine(self.db)
    
    def start_terminal(self):
        """Start terminal UI"""
        print("\n" + "="*70)
        print("  WHOAMISecGPT Money Machine v5.0 - TERMINAL MODE")
        print("="*70)
        ui = TerminalUI(self.engine)
        try:
            ui.run()
        except KeyboardInterrupt:
            print("\n\n💀 Stopped")
    
    def start_web(self):
        """Start web dashboard"""
        print("\n" + "="*70)
        print("  WHOAMISecGPT Money Machine v5.0 - WEB MODE")
        print("="*70)
        dashboard = WebDashboard(self.engine)
        dashboard.start()
    
    def start_dual(self):
        """Start both terminal and web"""
        # Start web in background
        web_thread = threading.Thread(target=self.start_web, daemon=True)
        web_thread.start()
        
        # Start terminal in main
        print("\n" + "="*70)
        print("  WHOAMISecGPT Money Machine v5.0 - DUAL MODE")
        print("  Terminal UI + Web Dashboard")
        print("="*70)
        
        # Wait a bit for web to start
        time.sleep(2)
        
        # Run terminal UI
        ui = TerminalUI(self.engine)
        try:
            ui.run()
        except KeyboardInterrupt:
            print("\n\n💀 System stopped")

# =============================================================================
# ENTRY POINT
# =============================================================================

def main():
    args = sys.argv[1:]
    
    print("""
╔══════════════════════════════════════════════════════════════════════╗
║     WHOAMISecGPT ULTIMATE MONEY MACHINE v5.0                         ║
║     Terminal UI + Web Dashboard + Crypto Arbitrage                   ║
╚══════════════════════════════════════════════════════════════════════╝
    """)
    
    controller = MainController()
    
    if '--web' in args:
        controller.start_web()
    elif '--terminal' in args or '--cli' in args:
        controller.start_terminal()
    else:
        print("🎮 Starting DUAL MODE (Terminal + Web)...\n")
        controller.start_dual()

if __name__ == "__main__":
    try:
        main()
    except KeyboardInterrupt:
        print("\n\n💀 WHOAMISecGPT stopped. Money extracted! 💰")
        sys.exit(0)
    except Exception as e:
        print(f"\n❌ Error: {e}")
        import traceback
        traceback.print_exc()
