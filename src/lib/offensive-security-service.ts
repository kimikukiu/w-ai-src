/**
 * Offensive Security Service - Integration of security tools for educational purposes
 * Based on WHOAMISec tools analysis - Laboratory use only
 */

import { PyodideRunner } from './pyodide-runner';
import { SwarmCoordinator } from './swarm-intelligence';

export interface SecurityScanResult {
  target: string;
  scanType: string;
  results: any[];
  timestamp: string;
  status: 'running' | 'completed' | 'error';
}

export interface NetworkScanOptions {
  target: string;
  ports?: string;
  scanType?: 'basic' | 'full' | 'stealth';
  timeout?: number;
}

export interface OSINTOptions {
  target: string;
  type: 'domain' | 'email' | 'ip' | 'subdomain';
  depth?: 'basic' | 'deep';
}

export class OffensiveSecurityService {
  private pyodideRunner: PyodideRunner;
  private swarmCoordinator: SwarmCoordinator;
  private scanResults: Map<string, SecurityScanResult> = new Map();

  constructor() {
    this.pyodideRunner = new PyodideRunner();
    this.swarmCoordinator = new SwarmCoordinator();
  }

  /**
   * Network Security Scanner - Educational port scanning
   */
  async networkScanner(options: NetworkScanOptions): Promise<SecurityScanResult> {
    const scanId = `network-${Date.now()}`;
    
    try {
      this.scanResults.set(scanId, {
        target: options.target,
        scanType: 'network',
        results: [],
        timestamp: new Date().toISOString(),
        status: 'running'
      });

      const pythonCode = `
import socket
import asyncio
import json
from datetime import datetime

async def port_scan(target, ports, timeout=3):
    results = []
    common_ports = [21, 22, 23, 25, 53, 80, 110, 135, 139, 143, 443, 993, 995, 1723, 3306, 3389, 5432, 5900, 8080, 8443]
    
    if ports == "common":
        ports_to_scan = common_ports
    else:
        ports_to_scan = [int(p) for p in ports.split(",") if p.isdigit()]
    
    async def check_port(port):
        try:
            sock = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
            sock.settimeout(timeout)
            result = sock.connect_ex((target, port))
            sock.close()
            
            if result == 0:
                service = get_service_name(port)
                return {
                    "port": port,
                    "status": "open",
                    "service": service,
                    "timestamp": datetime.now().isoformat()
                }
            return None
        except Exception as e:
            return None
    
    def get_service_name(port):
        services = {
            21: "FTP", 22: "SSH", 23: "Telnet", 25: "SMTP", 53: "DNS",
            80: "HTTP", 110: "POP3", 135: "RPC", 139: "NetBIOS", 143: "IMAP",
            443: "HTTPS", 993: "IMAPS", 995: "POP3S", 1723: "PPTP", 3306: "MySQL",
            3389: "RDP", 5432: "PostgreSQL", 5900: "VNC", 8080: "HTTP-Alt", 8443: "HTTPS-Alt"
        }
        return services.get(port, "Unknown")
    
    tasks = [check_port(port) for port in ports_to_scan]
    scan_results = await asyncio.gather(*tasks)
    
    return [r for r in scan_results if r is not None]

# Run scan
target = "${options.target}"
ports = "${options.ports || 'common'}"
results = asyncio.run(port_scan(target, ports))
print(json.dumps(results))
      `;

      const result = await this.pyodideRunner.runPythonScript(pythonCode);
      const scanResult: SecurityScanResult = {
        target: options.target,
        scanType: 'network',
        results: JSON.parse(result),
        timestamp: new Date().toISOString(),
        status: 'completed'
      };
      
      this.scanResults.set(scanId, scanResult);
      return scanResult;
      
    } catch (error) {
      const errorResult: SecurityScanResult = {
        target: options.target,
        scanType: 'network',
        results: [{ error: error.message }],
        timestamp: new Date().toISOString(),
        status: 'error'
      };
      this.scanResults.set(scanId, errorResult);
      return errorResult;
    }
  }

  /**
   * OSINT Scanner - Educational information gathering
   */
  async osintScanner(options: OSINTOptions): Promise<SecurityScanResult> {
    const scanId = `osint-${Date.now()}`;
    
    try {
      this.scanResults.set(scanId, {
        target: options.target,
        scanType: 'osint',
        results: [],
        timestamp: new Date().toISOString(),
        status: 'running'
      });

      const pythonCode = `
import json
import socket
import dns.resolver
import requests
from datetime import datetime
from urllib.parse import urlparse

def osint_domain(domain):
    results = []
    
    # DNS Information
    try:
        # A records
        answers = dns.resolver.resolve(domain, 'A')
        for rdata in answers:
            results.append({
                "type": "dns_a",
                "value": str(rdata),
                "description": f"A record for {domain}"
            })
    except Exception as e:
        results.append({
            "type": "dns_error",
            "value": str(e),
            "description": "DNS A record lookup failed"
        })
    
    # MX Records
    try:
        mx_answers = dns.resolver.resolve(domain, 'MX')
        for rdata in mx_answers:
            results.append({
                "type": "dns_mx",
                "value": str(rdata.exchange),
                "priority": rdata.preference,
                "description": f"MX record for {domain}"
            })
    except Exception as e:
        pass
    
    # Subdomain enumeration (basic)
    common_subdomains = ['www', 'mail', 'ftp', 'admin', 'blog', 'shop', 'api', 'dev', 'test']
    for sub in common_subdomains:
        try:
            full_domain = f"{sub}.{domain}"
            answers = dns.resolver.resolve(full_domain, 'A')
            results.append({
                "type": "subdomain_found",
                "value": full_domain,
                "description": f"Subdomain discovered: {full_domain}"
            })
        except:
            pass
    
    return results

def osint_ip(ip):
    results = []
    
    # Reverse DNS
    try:
        hostname = socket.gethostbyaddr(ip)[0]
        results.append({
            "type": "reverse_dns",
            "value": hostname,
            "description": f"Reverse DNS for {ip}"
        })
    except Exception as e:
        pass
    
    # Geolocation (basic)
    try:
        response = requests.get(f"http://ip-api.com/json/{ip}", timeout=5)
        if response.status_code == 200:
            data = response.json()
            if data.get('status') == 'success':
                results.append({
                    "type": "geolocation",
                    "country": data.get('country'),
                    "city": data.get('city'),
                    "isp": data.get('isp'),
                    "description": f"Geolocation for {ip}"
                })
    except Exception as e:
        pass
    
    return results

# Run OSINT
target = "${options.target}"
target_type = "${options.type}"

if target_type == "domain":
    results = osint_domain(target)
elif target_type == "ip":
    results = osint_ip(target)
else:
    results = [{"error": "Target type not supported"}]

print(json.dumps(results))
      `;

      const result = await this.pyodideRunner.runPythonScript(pythonCode);
      const scanResult: SecurityScanResult = {
        target: options.target,
        scanType: 'osint',
        results: JSON.parse(result),
        timestamp: new Date().toISOString(),
        status: 'completed'
      };
      
      this.scanResults.set(scanId, scanResult);
      return scanResult;
      
    } catch (error) {
      const errorResult: SecurityScanResult = {
        target: options.target,
        scanType: 'osint',
        results: [{ error: error.message }],
        timestamp: new Date().toISOString(),
        status: 'error'
      };
      this.scanResults.set(scanId, errorResult);
      return errorResult;
    }
  }

  /**
   * Vulnerability Scanner - Educational vulnerability assessment
   */
  async vulnerabilityScanner(target: string): Promise<SecurityScanResult> {
    const scanId = `vuln-${Date.now()}`;
    
    try {
      this.scanResults.set(scanId, {
        target,
        scanType: 'vulnerability',
        results: [],
        timestamp: new Date().toISOString(),
        status: 'running'
      });

      const pythonCode = `
import json
import requests
import socket
from datetime import datetime
from urllib.parse import urlparse

def basic_vulnerability_scan(target):
    results = []
    
    # Parse target
    if not target.startswith(('http://', 'https://')):
        target = f"http://{target}"
    
    parsed = urlparse(target)
    hostname = parsed.hostname
    
    # Basic web security checks
    security_headers = [
        'X-Frame-Options',
        'X-Content-Type-Options', 
        'X-XSS-Protection',
        'Strict-Transport-Security',
        'Content-Security-Policy'
    ]
    
    try:
        response = requests.get(target, timeout=10)
        
        # Check security headers
        missing_headers = []
        for header in security_headers:
            if header not in response.headers:
                missing_headers.append(header)
        
        if missing_headers:
            results.append({
                "type": "missing_security_headers",
                "severity": "medium",
                "headers": missing_headers,
                "description": f"Missing security headers: {', '.join(missing_headers)}"
            })
        
        # Check for common admin panels
        admin_paths = ['/admin', '/admin.php', '/administrator', '/wp-admin', '/cpanel']
        for path in admin_paths:
            try:
                admin_resp = requests.get(f"{target}{path}", timeout=5)
                if admin_resp.status_code == 200:
                    results.append({
                        "type": "admin_panel_exposed",
                        "path": path,
                        "severity": "medium",
                        "description": f"Admin panel may be accessible at {path}"
                    })
            except:
                pass
        
        # Check SSL/TLS (basic)
        if parsed.scheme == 'https':
            try:
                import ssl
                context = ssl.create_default_context()
                with socket.create_connection((hostname, 443), timeout=5) as sock:
                    with context.wrap_socket(sock, server_hostname=hostname) as ssock:
                        cert = ssock.getpeercert()
                        results.append({
                            "type": "ssl_certificate",
                            "subject": dict(x[0] for x in cert['subject']),
                            "issuer": dict(x[0] for x in cert['issuer']),
                            "expiry": cert['notAfter'],
                            "description": "SSL certificate information"
                        })
            except Exception as e:
                results.append({
                    "type": "ssl_error",
                    "error": str(e),
                    "severity": "high",
                    "description": "SSL/TLS configuration issue"
                })
        
    except Exception as e:
        results.append({
            "type": "connection_error",
            "error": str(e),
            "severity": "high",
            "description": "Could not connect to target"
        })
    
    return results

# Run vulnerability scan
target = "${target}"
results = basic_vulnerability_scan(target)
print(json.dumps(results))
      `;

      const result = await this.pyodideRunner.runPythonScript(pythonCode);
      const scanResult: SecurityScanResult = {
        target,
        scanType: 'vulnerability',
        results: JSON.parse(result),
        timestamp: new Date().toISOString(),
        status: 'completed'
      };
      
      this.scanResults.set(scanId, scanResult);
      return scanResult;
      
    } catch (error) {
      const errorResult: SecurityScanResult = {
        target,
        scanType: 'vulnerability',
        results: [{ error: error.message }],
        timestamp: new Date().toISOString(),
        status: 'error'
      };
      this.scanResults.set(scanId, errorResult);
      return errorResult;
    }
  }

  /**
   * Get scan results by ID
   */
  getScanResult(scanId: string): SecurityScanResult | undefined {
    return this.scanResults.get(scanId);
  }

  /**
   * Get all scan results
   */
  getAllScanResults(): SecurityScanResult[] {
    return Array.from(this.scanResults.values());
  }

  /**
   * Clear old scan results
   */
  clearOldResults(maxAge: number = 3600000): void { // 1 hour default
    const now = Date.now();
    for (const [id, result] of this.scanResults.entries()) {
      const resultTime = new Date(result.timestamp).getTime();
      if (now - resultTime > maxAge) {
        this.scanResults.delete(id);
      }
    }
  }

  /**
   * Run coordinated security scan using SWARM intelligence
   */
  async runCoordinatedScan(target: string, scanTypes: string[]): Promise<SecurityScanResult[]> {
    const tasks = scanTypes.map(scanType => {
      switch (scanType) {
        case 'network':
          return this.networkScanner({ target, ports: 'common' });
        case 'osint':
          return this.osintScanner({ target, type: 'domain' });
        case 'vulnerability':
          return this.vulnerabilityScanner(target);
        default:
          return Promise.resolve({
            target,
            scanType,
            results: [{ error: 'Unknown scan type' }],
            timestamp: new Date().toISOString(),
            status: 'error' as const
          });
      }
    });

    return Promise.all(tasks);
  }
}

// Singleton instance
export const offensiveSecurityService = new OffensiveSecurityService();