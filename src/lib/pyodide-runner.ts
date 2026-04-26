/**
 * Pyodide Runner - Python execution in browser for security tools
 * Educational purposes only - Laboratory use
 */

export interface PyodideResult {
  output: string;
  error?: string;
  success: boolean;
}

export class PyodideRunner {
  private pyodide: any = null;
  private isInitialized: boolean = false;

  async initialize(): Promise<void> {
    if (this.isInitialized) return;

    try {
      // Load Pyodide from CDN
      const script = document.createElement('script');
      script.src = 'https://cdn.jsdelivr.net/pyodide/v0.24.1/full/pyodide.js';
      document.head.appendChild(script);

      await new Promise<void>((resolve) => {
        script.onload = async () => {
          // @ts-ignore - Pyodide global
          this.pyodide = await loadPyodide({
            indexURL: 'https://cdn.jsdelivr.net/pyodide/v0.24.1/full/'
          });
          this.isInitialized = true;
          resolve();
        };
      });
    } catch (error) {
      console.error('Failed to initialize Pyodide:', error);
      throw new Error('Pyodide initialization failed');
    }
  }

  async runPythonScript(code: string): Promise<string> {
    if (!this.isInitialized) {
      await this.initialize();
    }

    try {
      // Install required packages if needed
      await this.installPackages(['requests', 'dnspython']);
      
      // Run the Python code
      const result = await this.pyodide.runPythonAsync(code);
      return result?.toString() || '';
    } catch (error) {
      console.error('Python execution error:', error);
      throw new Error(`Python execution failed: ${error.message}`);
    }
  }

  async installPackages(packages: string[]): Promise<void> {
    if (!this.isInitialized) {
      await this.initialize();
    }

    try {
      const micropip = this.pyodide.pyimport('micropip');
      for (const pkg of packages) {
        await micropip.install(pkg);
      }
    } catch (error) {
      console.warn('Package installation failed, continuing with basic functionality:', error);
    }
  }

  async runSecurityScript(scriptType: string, target: string, options: any = {}): Promise<PyodideResult> {
    try {
      let pythonCode = '';

      switch (scriptType) {
        case 'port_scan':
          pythonCode = this.generatePortScanCode(target, options);
          break;
        case 'osint':
          pythonCode = this.generateOSINTCode(target, options);
          break;
        case 'vulnerability':
          pythonCode = this.generateVulnerabilityCode(target, options);
          break;
        default:
          throw new Error(`Unknown script type: ${scriptType}`);
      }

      const output = await this.runPythonScript(pythonCode);
      
      return {
        output,
        success: true
      };
    } catch (error) {
      return {
        output: '',
        error: error.message,
        success: false
      };
    }
  }

  private generatePortScanCode(target: string, options: any): string {
    return `
import socket
import json
import asyncio

async def port_scan(target, ports, timeout=3):
    results = []
    common_ports = [21, 22, 23, 25, 53, 80, 110, 135, 139, 143, 443, 993, 995, 1723, 3306, 3389, 5432, 5900, 8080, 8443]
    
    ports_to_scan = common_ports if ports == 'common' else [int(p) for p in str(ports).split(',') if p.isdigit()]
    
    async def check_port(port):
        try:
            sock = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
            sock.settimeout(timeout)
            result = sock.connect_ex((target, port))
            sock.close()
            
            if result == 0:
                services = {
                    21: "FTP", 22: "SSH", 23: "Telnet", 25: "SMTP", 53: "DNS",
                    80: "HTTP", 110: "POP3", 135: "RPC", 139: "NetBIOS", 143: "IMAP",
                    443: "HTTPS", 993: "IMAPS", 995: "POP3S", 1723: "PPTP", 3306: "MySQL",
                    3389: "RDP", 5432: "PostgreSQL", 5900: "VNC", 8080: "HTTP-Alt", 8443: "HTTPS-Alt"
                }
                return {
                    "port": port,
                    "status": "open",
                    "service": services.get(port, "Unknown"),
                    "timestamp": __import__('datetime').datetime.now().isoformat()
                }
            return None
        except Exception:
            return None
    
    tasks = [check_port(port) for port in ports_to_scan]
    scan_results = await asyncio.gather(*tasks)
    return [r for r in scan_results if r is not None]

# Run scan
target = "${target}"
ports = "${options.ports || 'common'}"
results = asyncio.run(port_scan(target, ports))
print(json.dumps(results))
    `;
  }

  private generateOSINTCode(target: string, options: any): string {
    return `
import json
import socket
from datetime import datetime

def osint_domain(domain):
    results = []
    
    # Basic DNS lookup
    try:
        ip = socket.gethostbyname(domain)
        results.append({
            "type": "dns_a",
            "value": ip,
            "description": f"A record for {domain}",
            "timestamp": datetime.now().isoformat()
        })
    except Exception as e:
        results.append({
            "type": "dns_error",
            "value": str(e),
            "description": "DNS lookup failed"
        })
    
    # Subdomain enumeration (basic)
    common_subdomains = ['www', 'mail', 'ftp', 'admin', 'blog', 'shop', 'api', 'dev', 'test']
    for sub in common_subdomains:
        try:
            full_domain = f"{sub}.{domain}"
            socket.gethostbyname(full_domain)
            results.append({
                "type": "subdomain_found",
                "value": full_domain,
                "description": f"Subdomain discovered: {full_domain}"
            })
        except:
            pass
    
    return results

# Run OSINT
target = "${target}"
results = osint_domain(target)
print(json.dumps(results))
    `;
  }

  private generateVulnerabilityCode(target: string, options: any): string {
    return `
import json
import urllib.request
import urllib.error
from datetime import datetime

def basic_security_check(url):
    results = []
    
    if not url.startswith(('http://', 'https://')):
        url = f"http://{url}"
    
    try:
        # Check if site is accessible
        response = urllib.request.urlopen(url, timeout=10)
        
        # Basic header security check
        headers = dict(response.headers)
        security_headers = [
            'X-Frame-Options',
            'X-Content-Type-Options', 
            'X-XSS-Protection',
            'Strict-Transport-Security',
            'Content-Security-Policy'
        ]
        
        missing_headers = []
        for header in security_headers:
            if header not in headers:
                missing_headers.append(header)
        
        if missing_headers:
            results.append({
                "type": "missing_security_headers",
                "severity": "medium",
                "headers": missing_headers,
                "description": f"Missing security headers: {', '.join(missing_headers)}"
            })
        
        results.append({
            "type": "accessibility_check",
            "status": "accessible",
            "code": response.getcode(),
            "description": f"Target is accessible (HTTP {response.getcode()})"
        })
        
    except urllib.error.URLError as e:
        results.append({
            "type": "accessibility_error",
            "error": str(e),
            "severity": "high",
            "description": "Target is not accessible"
        })
    
    return results

# Run vulnerability check
target = "${target}"
results = basic_security_check(target)
print(json.dumps(results))
    `;
  }

  isReady(): boolean {
    return this.isInitialized;
  }
}

// Singleton instance
export const pyodideRunner = new PyodideRunner();