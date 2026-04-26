/**
 * Email OSINT Tool - Educational email intelligence gathering
 * Based on OSINT concepts for authorized security testing
 */

import { PyodideRunner } from './pyodide-runner';

export interface EmailOSINTResult {
  email: string;
  type: 'breach_check' | 'domain_analysis' | 'social_media' | 'format_validation';
  results: any[];
  timestamp: string;
  status: 'running' | 'completed' | 'error';
}

export interface EmailValidationResult {
  email: string;
  isValid: boolean;
  domain: string;
  format: boolean;
  domainExists: boolean;
  hasMXRecords: boolean;
  riskLevel: 'low' | 'medium' | 'high';
}

export class EmailOSINTService {
  private pyodideRunner: PyodideRunner;
  private results: Map<string, EmailOSINTResult> = new Map();

  constructor() {
    this.pyodideRunner = new PyodideRunner();
  }

  /**
   * Email validation and format checking
   */
  async validateEmail(email: string): Promise<EmailValidationResult> {
    try {
      const pythonCode = `
import re
import socket
import dns.resolver
from datetime import datetime

def validate_email(email):
    result = {
        "email": email,
        "isValid": False,
        "domain": "",
        "format": False,
        "domainExists": False,
        "hasMXRecords": False,
        "riskLevel": "high"
    }
    
    # Basic format validation
    email_pattern = r'^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$'
    result["format"] = bool(re.match(email_pattern, email))
    
    if not result["format"]:
        return result
    
    # Extract domain
    try:
        domain = email.split('@')[1]
        result["domain"] = domain
    except IndexError:
        return result
    
    # Domain validation
    try:
        # Check if domain has DNS records
        answers = dns.resolver.resolve(domain, 'A')
        result["domainExists"] = len(answers) > 0
    except Exception:
        result["domainExists"] = False
    
    # MX records check
    try:
        mx_answers = dns.resolver.resolve(domain, 'MX')
        result["hasMXRecords"] = len(mx_answers) > 0
    except Exception:
        result["hasMXRecords"] = False
    
    # Risk assessment
    if result["format"] and result["domainExists"] and result["hasMXRecords"]:
        result["isValid"] = True
        result["riskLevel"] = "low"
    elif result["format"] and result["domainExists"]:
        result["riskLevel"] = "medium"
    else:
        result["riskLevel"] = "high"
    
    return result

# Run validation
email = "${email}"
result = validate_email(email)
print(json.dumps(result))
      `;

      const result = await this.pyodideRunner.runPythonScript(pythonCode);
      return JSON.parse(result);
    } catch (error) {
      return {
        email,
        isValid: false,
        domain: '',
        format: false,
        domainExists: false,
        hasMXRecords: false,
        riskLevel: 'high'
      };
    }
  }

  /**
   * Domain intelligence gathering for email domain
   */
  async analyzeEmailDomain(email: string): Promise<EmailOSINTResult> {
    const resultId = `domain-${Date.now()}`;
    
    try {
      this.results.set(resultId, {
        email,
        type: 'domain_analysis',
        results: [],
        timestamp: new Date().toISOString(),
        status: 'running'
      });

      const pythonCode = `
import json
import socket
import dns.resolver
import whois
from datetime import datetime

def analyze_domain(email):
    results = []
    
    try:
        domain = email.split('@')[1]
        results.append({
            "type": "domain_extracted",
            "domain": domain,
            "timestamp": datetime.now().isoformat()
        })
    except IndexError:
        return [{"error": "Invalid email format"}]
    
    # DNS Analysis
    try:
        # A records
        a_records = dns.resolver.resolve(domain, 'A')
        for record in a_records:
            results.append({
                "type": "dns_a_record",
                "ip": str(record),
                "timestamp": datetime.now().isoformat()
            })
    except Exception as e:
        results.append({
            "type": "dns_a_error",
            "error": str(e),
            "timestamp": datetime.now().isoformat()
        })
    
    # MX Records
    try:
        mx_records = dns.resolver.resolve(domain, 'MX')
        for record in mx_records:
            results.append({
                "type": "dns_mx_record",
                "exchange": str(record.exchange),
                "priority": record.preference,
                "timestamp": datetime.now().isoformat()
            })
    except Exception as e:
        pass
    
    # TXT Records (SPF, DMARC)
    try:
        txt_records = dns.resolver.resolve(domain, 'TXT')
        for record in txt_records:
            txt_data = str(record).strip('"')
            if txt_data.startswith('v=spf1'):
                results.append({
                    "type": "spf_record",
                    "data": txt_data,
                    "timestamp": datetime.now().isoformat()
                })
            elif txt_data.startswith('v=DMARC1'):
                results.append({
                    "type": "dmarc_record", 
                    "data": txt_data,
                    "timestamp": datetime.now().isoformat()
                })
    except Exception as e:
        pass
    
    # WHOIS Information (basic)
    try:
        domain_info = whois.whois(domain)
        results.append({
            "type": "whois_info",
            "registrar": str(domain_info.registrar) if domain_info.registrar else "Unknown",
            "creation_date": str(domain_info.creation_date) if domain_info.creation_date else "Unknown",
            "expiration_date": str(domain_info.expiration_date) if domain_info.expiration_date else "Unknown",
            "timestamp": datetime.now().isoformat()
        })
    except Exception as e:
        results.append({
            "type": "whois_error",
            "error": str(e),
            "timestamp": datetime.now().isoformat()
        })
    
    return results

# Run analysis
email = "${email}"
results = analyze_domain(email)
print(json.dumps(results))
      `;

      const result = await this.pyodideRunner.runPythonScript(pythonCode);
      const analysisResult: EmailOSINTResult = {
        email,
        type: 'domain_analysis',
        results: JSON.parse(result),
        timestamp: new Date().toISOString(),
        status: 'completed'
      };
      
      this.results.set(resultId, analysisResult);
      return analysisResult;
      
    } catch (error) {
      const errorResult: EmailOSINTResult = {
        email,
        type: 'domain_analysis',
        results: [{ error: error.message }],
        timestamp: new Date().toISOString(),
        status: 'error'
      };
      this.results.set(resultId, errorResult);
      return errorResult;
    }
  }

  /**
   * Breach database simulation check
   */
  async checkBreachStatus(email: string): Promise<EmailOSINTResult> {
    const resultId = `breach-${Date.now()}`;
    
    try {
      this.results.set(resultId, {
        email,
        type: 'breach_check',
        results: [],
        timestamp: new Date().toISOString(),
        status: 'running'
      });

      const pythonCode = `
import json
import hashlib
from datetime import datetime

def simulate_breach_check(email):
    results = []
    
    # Simulate hash-based breach check
    email_hash = hashlib.sha256(email.encode()).hexdigest()
    
    # Simulate common breach patterns (educational only)
    breach_simulations = [
        {
            "name": "LinkedIn 2012",
            "date": "2012-06-05",
            "affected_accounts": 165000000,
            "data_types": ["email", "password_hashes"]
        },
        {
            "name": "Adobe 2013", 
            "date": "2013-10-03",
            "affected_accounts": 153000000,
            "data_types": ["email", "password_hashes", "password_hints"]
        },
        {
            "name": "Yahoo 2014",
            "date": "2014-09-22", 
            "affected_accounts": 500000000,
            "data_types": ["email", "password_hashes", "personal_info"]
        }
    ]
    
    # Simulate check based on email hash pattern
    hash_int = int(email_hash[:8], 16)
    
    for breach in breach_simulations:
        # Simulate 30% chance of being in breach (educational)
        if (hash_int % 100) < 30:
            results.append({
                "type": "breach_found",
                "breach_name": breach["name"],
                "breach_date": breach["date"],
                "affected_accounts": breach["affected_accounts"],
                "data_types_exposed": breach["data_types"],
                "recommendation": "Change passwords and enable 2FA",
                "severity": "high",
                "timestamp": datetime.now().isoformat()
            })
    
    if not results:
        results.append({
            "type": "no_known_breaches",
            "message": "No known breaches found for this email address",
            "recommendation": "Continue good security practices",
            "timestamp": datetime.now().isoformat()
        })
    
    return results

# Run breach check
email = "${email}"
results = simulate_breach_check(email)
print(json.dumps(results))
      `;

      const result = await this.pyodideRunner.runPythonScript(pythonCode);
      const breachResult: EmailOSINTResult = {
        email,
        type: 'breach_check',
        results: JSON.parse(result),
        timestamp: new Date().toISOString(),
        status: 'completed'
      };
      
      this.results.set(resultId, breachResult);
      return breachResult;
      
    } catch (error) {
      const errorResult: EmailOSINTResult = {
        email,
        type: 'breach_check',
        results: [{ error: error.message }],
        timestamp: new Date().toISOString(),
        status: 'error'
      };
      this.results.set(resultId, errorResult);
      return errorResult;
    }
  }

  /**
   * Complete email OSINT analysis
   */
  async runCompleteEmailOSINT(email: string): Promise<{
    validation: EmailValidationResult;
    domainAnalysis: EmailOSINTResult;
    breachCheck: EmailOSINTResult;
  }> {
    const [validation, domainAnalysis, breachCheck] = await Promise.all([
      this.validateEmail(email),
      this.analyzeEmailDomain(email),
      this.checkBreachStatus(email)
    ]);

    return {
      validation,
      domainAnalysis,
      breachCheck
    };
  }

  /**
   * Get result by ID
   */
  getResult(resultId: string): EmailOSINTResult | undefined {
    return this.results.get(resultId);
  }

  /**
   * Get all results
   */
  getAllResults(): EmailOSINTResult[] {
    return Array.from(this.results.values());
  }

  /**
   * Clear old results
   */
  clearOldResults(maxAge: number = 3600000): void {
    const now = Date.now();
    for (const [id, result] of this.results.entries()) {
      const resultTime = new Date(result.timestamp).getTime();
      if (now - resultTime > maxAge) {
        this.results.delete(id);
      }
    }
  }
}

// Singleton instance
export const emailOSINTService = new EmailOSINTService();