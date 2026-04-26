'use client';

import { useState, useEffect } from 'react';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import { Textarea } from '@/components/ui/textarea';
import { Badge } from '@/components/ui/badge';
import { Alert, AlertDescription } from '@/components/ui/alert';
import { 
  Search, 
  Download, 
  Upload, 
  Target, 
  Shield, 
  Globe,
  Mail,
  User,
  Building,
  MapPin,
  Briefcase,
  GraduationCap,
  Star,
  Users,
  Activity,
  AlertTriangle,
  CheckCircle,
  XCircle,
  RefreshCw
} from 'lucide-react';
import EmailExtractorsPro, { 
  EmailResult, 
  DomainIntelligence, 
  ProfessionalProfile,
  BreachData,
  SocialMediaProfile 
} from '@/lib/email-extractors-pro-complete';

export default function EmailExtractorsProInterface() {
  const [extractor] = useState(() => new EmailExtractorsPro());
  const [email, setEmail] = useState('');
  const [domain, setDomain] = useState('');
  const [firstName, setFirstName] = useState('');
  const [lastName, setLastName] = useState('');
  const [company, setCompany] = useState('');
  const [url, setUrl] = useState('');
  const [bulkEmails, setBulkEmails] = useState('');
  const [consoleOutput, setConsoleOutput] = useState<string[]>([]);
  const [isLoading, setIsLoading] = useState(false);
  const [searchResults, setSearchResults] = useState<EmailResult[]>([]);
  const [domainResults, setDomainResults] = useState<DomainIntelligence | null>(null);
  const [professionalResults, setProfessionalResults] = useState<ProfessionalProfile | null>(null);
  const [breachResults, setBreachResults] = useState<BreachData | null>(null);
  const [socialResults, setSocialResults] = useState<SocialMediaProfile[]>([]);
  const [bulkResults, setBulkResults] = useState<any[]>([]);
  const [statistics, setStatistics] = useState<any>(null);

  const addConsoleMessage = (message: string, type: 'info' | 'success' | 'error' = 'info') => {
    const timestamp = new Date().toLocaleTimeString();
    const prefix = type === 'success' ? '✅' : type === 'error' ? '❌' : 'ℹ️';
    setConsoleOutput(prev => [...prev, `[${timestamp}] ${prefix} ${message}`]);
  };

  const validateEmail = (email: string): boolean => {
    return extractor.validator.validateEmail(email);
  };

  const extractSingleEmail = async () => {
    if (!email) {
      addConsoleMessage('Please enter an email address', 'error');
      return;
    }

    if (!validateEmail(email)) {
      addConsoleMessage('Invalid email format', 'error');
      return;
    }

    setIsLoading(true);
    addConsoleMessage(`Starting full OSINT analysis for ${email}...`, 'info');

    try {
      const result = await extractor.performFullOSINT(email);
      
      // Update results
      setProfessionalResults(result.professionalProfile);
      setSocialResults(result.socialProfiles);
      setBreachResults(result.breachData);
      
      addConsoleMessage(`OSINT analysis completed for ${email}`, 'success');
      
      // Check breach status
      if (result.breachData?.breaches?.length > 0) {
        addConsoleMessage(`⚠️ Email found in ${result.breachData.breaches.length} breaches`, 'error');
      } else {
        addConsoleMessage('✅ No breaches detected for this email', 'success');
      }
      
    } catch (error) {
      addConsoleMessage(`Error during analysis: ${error}`, 'error');
    } finally {
      setIsLoading(false);
    }
  };

  const extractFromDomain = async () => {
    if (!domain) {
      addConsoleMessage('Please enter a domain', 'error');
      return;
    }

    setIsLoading(true);
    addConsoleMessage(`Extracting emails from domain ${domain}...`, 'info');

    try {
      const result = await extractor.extractFromDomain(domain);
      setDomainResults(result);
      setSearchResults(result.emails);
      
      addConsoleMessage(`Found ${result.emails.length} emails from ${domain}`, 'success');
      addConsoleMessage(`Discovered ${result.subdomains.length} subdomains`, 'info');
      addConsoleMessage(`Detected ${result.technologies.length} technologies`, 'info');
      
    } catch (error) {
      addConsoleMessage(`Error extracting from domain: ${error}`, 'error');
    } finally {
      setIsLoading(false);
    }
  };

  const extractFromURL = async () => {
    if (!url) {
      addConsoleMessage('Please enter a URL', 'error');
      return;
    }

    setIsLoading(true);
    addConsoleMessage(`Scraping emails from ${url}...`, 'info');

    try {
      const emails = await extractor.extractFromURL(url);
      setSearchResults(emails);
      
      addConsoleMessage(`Found ${emails.length} emails from ${url}`, 'success');
      
    } catch (error) {
      addConsoleMessage(`Error scraping URL: ${error}`, 'error');
    } finally {
      setIsLoading(false);
    }
  };

  const generateEmailPermutations = () => {
    if (!firstName || !lastName || !company) {
      addConsoleMessage('Please enter first name, last name, and company', 'error');
      return;
    }

    const permutations = extractor.generateEmailPermutations(firstName, lastName, company);
    setSearchResults(permutations.map(email => ({
      email,
      source: 'generated',
      confidence: 0.5,
      lastSeen: new Date().toISOString()
    })));
    
    addConsoleMessage(`Generated ${permutations.length} email permutations`, 'success');
  };

  const extractBulk = async () => {
    if (!bulkEmails) {
      addConsoleMessage('Please enter email addresses', 'error');
      return;
    }

    const emails = bulkEmails.split('\n').map(e => e.trim()).filter(e => e);
    
    if (emails.length === 0) {
      addConsoleMessage('No valid email addresses found', 'error');
      return;
    }

    setIsLoading(true);
    addConsoleMessage(`Starting bulk extraction for ${emails.length} emails...`, 'info');

    try {
      const results = await extractor.bulkExtract(emails);
      setBulkResults(results);
      
      // Generate statistics
      const stats = extractor.getStatistics(results);
      setStatistics(stats);
      
      addConsoleMessage(`Bulk extraction completed for ${emails.length} emails`, 'success');
      addConsoleMessage(`Found ${stats.breached} emails in breaches`, 'info');
      addConsoleMessage(`${stats.withSocialProfiles} emails have social profiles`, 'info');
      
    } catch (error) {
      addConsoleMessage(`Error during bulk extraction: ${error}`, 'error');
    } finally {
      setIsLoading(false);
    }
  };

  const checkBreachStatus = async () => {
    if (!email) {
      addConsoleMessage('Please enter an email address', 'error');
      return;
    }

    setIsLoading(true);
    addConsoleMessage(`Checking breach status for ${email}...`, 'info');

    try {
      const result = await extractor.checkBreachStatus(email);
      setBreachResults(result);
      
      if (result.breaches.length > 0) {
        addConsoleMessage(`⚠️ Found in ${result.breaches.length} breaches`, 'error');
        result.breaches.forEach(breach => {
          addConsoleMessage(`  - ${breach.name} (${breach.date})`, 'error');
        });
      } else {
        addConsoleMessage('✅ No breaches detected', 'success');
      }
      
      if (result.pastes.length > 0) {
        addConsoleMessage(`Found in ${result.pastes.length} pastes`, 'info');
      }
      
    } catch (error) {
      addConsoleMessage(`Error checking breach status: ${error}`, 'error');
    } finally {
      setIsLoading(false);
    }
  };

  const exportResults = (format: 'json' | 'csv' | 'txt') => {
    let data: string;
    let filename: string;
    
    switch (format) {
      case 'json':
        data = JSON.stringify({
          searchResults,
          domainResults,
          professionalResults,
          breachResults,
          socialResults,
          bulkResults,
          statistics,
          timestamp: new Date().toISOString()
        }, null, 2);
        filename = `email-extractors-results-${Date.now()}.json`;
        break;
        
      case 'csv':
        const csvData = [
          'Email,Source,Confidence,Domain,Disposable,RoleBased,Valid',
          ...searchResults.map(result => 
            `${result.email},${result.source},${result.confidence},${extractor.validator.extractDomain(result.email)},${extractor.validator.isDisposable(result.email)},${extractor.validator.isRoleBased(result.email)},${extractor.validator.validateEmail(result.email)}`
          )
        ].join('\n');
        data = csvData;
        filename = `email-extractors-results-${Date.now()}.csv`;
        break;
        
      case 'txt':
        const txtData = searchResults.map(result => result.email).join('\n');
        data = txtData;
        filename = `email-extractors-results-${Date.now()}.txt`;
        break;
    }
    
    const blob = new Blob([data], { type: 'text/plain' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = filename;
    a.click();
    URL.revokeObjectURL(url);
    
    addConsoleMessage(`Results exported as ${format.toUpperCase()}`, 'success');
  };

  const clearConsole = () => {
    setConsoleOutput([]);
  };

  const getSeverityColor = (severity: string) => {
    switch (severity) {
      case 'Critical': return 'bg-red-600';
      case 'High': return 'bg-orange-600';
      case 'Medium': return 'bg-yellow-600';
      case 'Low': return 'bg-blue-600';
      default: return 'bg-gray-600';
    }
  };

  const getConfidenceColor = (confidence: number) => {
    if (confidence >= 0.9) return 'bg-green-600';
    if (confidence >= 0.7) return 'bg-yellow-600';
    if (confidence >= 0.5) return 'bg-orange-600';
    return 'bg-red-600';
  };

  return (
    <div className="min-h-screen bg-black text-green-400 p-6">
      <div className="max-w-7xl mx-auto">
        <div className="text-center mb-8">
          <h1 className="text-4xl font-bold text-blue-500 mb-2 flex items-center justify-center gap-3">
            <Mail className="w-10 h-10" />
            Email Extractors Pro
            <Mail className="w-10 h-10" />
          </h1>
          <p className="text-green-300 text-lg">
            Advanced email OSINT and intelligence gathering platform
          </p>
        </div>

        <Tabs defaultValue="single" className="space-y-6">
          <TabsList className="bg-gray-900 border border-blue-500">
            <TabsTrigger value="single" className="data-[state=active]:bg-blue-900">
              <User className="w-4 h-4 mr-2" />
              Single Email
            </TabsTrigger>
            <TabsTrigger value="domain" className="data-[state=active]:bg-blue-900">
              <Globe className="w-4 h-4 mr-2" />
              Domain Search
            </TabsTrigger>
            <TabsTrigger value="url" className="data-[state=active]:bg-blue-900">
              <Globe className="w-4 h-4 mr-2" />
              URL Scraping
            </TabsTrigger>
            <TabsTrigger value="generate" className="data-[state=active]:bg-blue-900">
              <Users className="w-4 h-4 mr-2" />
              Generate Permutations
            </TabsTrigger>
            <TabsTrigger value="bulk" className="data-[state=active]:bg-blue-900">
              <Upload className="w-4 h-4 mr-2" />
              Bulk Extract
            </TabsTrigger>
            <TabsTrigger value="breach" className="data-[state=active]:bg-blue-900">
              <Shield className="w-4 h-4 mr-2" />
              Breach Check
            </TabsTrigger>
          </TabsList>

          {/* Single Email Tab */}
          <TabsContent value="single">
            <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
              <Card className="bg-gray-900 border border-blue-500">
                <CardHeader>
                  <CardTitle className="text-blue-400">Email OSINT Analysis</CardTitle>
                  <CardDescription>Perform comprehensive analysis on single email</CardDescription>
                </CardHeader>
                <CardContent className="space-y-4">
                  <div>
                    <Label htmlFor="email">Email Address</Label>
                    <Input
                      id="email"
                      value={email}
                      onChange={(e) => setEmail(e.target.value)}
                      placeholder="john.doe@example.com"
                      className="bg-gray-800 border-blue-500 text-green-300"
                    />
                  </div>
                  
                  <div className="flex gap-2">
                    <Button 
                      onClick={extractSingleEmail}
                      disabled={isLoading}
                      className="bg-blue-600 hover:bg-blue-700"
                    >
                      <Search className="w-4 h-4 mr-2" />
                      Analyze Email
                    </Button>
                    
                    <Button 
                      onClick={checkBreachStatus}
                      disabled={isLoading}
                      className="bg-red-600 hover:bg-red-700"
                    >
                      <Shield className="w-4 h-4 mr-2" />
                      Check Breaches
                    </Button>
                  </div>
                </CardContent>
              </Card>

              <Card className="bg-gray-900 border border-blue-500">
                <CardHeader>
                  <CardTitle className="text-blue-400">Email Validation</CardTitle>
                  <CardDescription>Real-time validation results</CardDescription>
                </CardHeader>
                <CardContent>
                  {email && (
                    <div className="space-y-2">
                      <div className="flex justify-between">
                        <span className="text-green-300">Format:</span>
                        <Badge className={validateEmail(email) ? 'bg-green-600' : 'bg-red-600'}>
                          {validateEmail(email) ? 'Valid' : 'Invalid'}
                        </Badge>
                      </div>
                      <div className="flex justify-between">
                        <span className="text-green-300">Domain:</span>
                        <span className="text-blue-300">{extractor.validator.extractDomain(email)}</span>
                      </div>
                      <div className="flex justify-between">
                        <span className="text-green-300">Disposable:</span>
                        <Badge className={extractor.validator.isDisposable(email) ? 'bg-red-600' : 'bg-green-600'}>
                          {extractor.validator.isDisposable(email) ? 'Yes' : 'No'}
                        </Badge>
                      </div>
                      <div className="flex justify-between">
                        <span className="text-green-300">Role-based:</span>
                        <Badge className={extractor.validator.isRoleBased(email) ? 'bg-yellow-600' : 'bg-green-600'}>
                          {extractor.validator.isRoleBased(email) ? 'Yes' : 'No'}
                        </Badge>
                      </div>
                    </div>
                  )}
                  {!email && (
                    <div className="text-center text-green-300 py-4">
                      Enter an email to see validation results
                    </div>
                  )}
                </CardContent>
              </Card>
            </div>
          </TabsContent>

          {/* Domain Search Tab */}
          <TabsContent value="domain">
            <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
              <Card className="bg-gray-900 border border-blue-500">
                <CardHeader>
                  <CardTitle className="text-blue-400">Domain Intelligence</CardTitle>
                  <CardDescription>Extract emails and intelligence from domain</CardDescription>
                </CardHeader>
                <CardContent className="space-y-4">
                  <div>
                    <Label htmlFor="domain">Domain Name</Label>
                    <Input
                      id="domain"
                      value={domain}
                      onChange={(e) => setDomain(e.target.value)}
                      placeholder="example.com"
                      className="bg-gray-800 border-blue-500 text-green-300"
                    />
                  </div>
                  
                  <Button 
                    onClick={extractFromDomain}
                    disabled={isLoading}
                    className="bg-blue-600 hover:bg-blue-700 w-full"
                  >
                    <Globe className="w-4 h-4 mr-2" />
                    Extract from Domain
                  </Button>
                </CardContent>
              </Card>

              <Card className="bg-gray-900 border border-blue-500">
                <CardHeader>
                  <CardTitle className="text-blue-400">Domain Results</CardTitle>
                  <CardDescription>Intelligence gathered</CardDescription>
                </CardHeader>
                <CardContent>
                  {domainResults && (
                    <div className="space-y-2">
                      <div className="flex justify-between">
                        <span className="text-green-300">Emails Found:</span>
                        <Badge className="bg-blue-600">{domainResults.emails.length}</Badge>
                      </div>
                      <div className="flex justify-between">
                        <span className="text-green-300">Subdomains:</span>
                        <Badge className="bg-green-600">{domainResults.subdomains.length}</Badge>
                      </div>
                      <div className="flex justify-between">
                        <span className="text-green-300">Technologies:</span>
                        <Badge className="bg-purple-600">{domainResults.technologies.length}</Badge>
                      </div>
                      <div className="flex justify-between">
                        <span className="text-green-300">Registrar:</span>
                        <span className="text-blue-300">{domainResults.whois.registrar}</span>
                      </div>
                    </div>
                  )}
                  {!domainResults && (
                    <div className="text-center text-green-300 py-4">
                      Search a domain to see results
                    </div>
                  )}
                </CardContent>
              </Card>
            </div>
          </TabsContent>

          {/* URL Scraping Tab */}
          <TabsContent value="url">
            <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
              <Card className="bg-gray-900 border border-blue-500">
                <CardHeader>
                  <CardTitle className="text-blue-400">URL Scraping</CardTitle>
                  <CardDescription>Scrape emails from web pages</CardDescription>
                </CardHeader>
                <CardContent className="space-y-4">
                  <div>
                    <Label htmlFor="url">URL</Label>
                    <Input
                      id="url"
                      value={url}
                      onChange={(e) => setUrl(e.target.value)}
                      placeholder="https://example.com/contact"
                      className="bg-gray-800 border-blue-500 text-green-300"
                    />
                  </div>
                  
                  <Button 
                    onClick={extractFromURL}
                    disabled={isLoading}
                    className="bg-blue-600 hover:bg-blue-700 w-full"
                  >
                    <Globe className="w-4 h-4 mr-2" />
                    Scrape Emails
                  </Button>
                </CardContent>
              </Card>

              <Card className="bg-gray-900 border border-blue-500">
                <CardHeader>
                  <CardTitle className="text-blue-400">Scraping Results</CardTitle>
                  <CardDescription>Emails found on page</CardDescription>
                </CardHeader>
                <CardContent>
                  {searchResults.length > 0 && (
                    <div className="space-y-2 max-h-48 overflow-y-auto">
                      {searchResults.map((result, index) => (
                        <div key={index} className="flex justify-between items-center p-2 bg-gray-800 rounded">
                          <span className="text-green-300 font-mono text-sm">{result.email}</span>
                          <Badge className={getConfidenceColor(result.confidence)}>
                            {(result.confidence * 100).toFixed(0)}%
                          </Badge>
                        </div>
                      ))}
                    </div>
                  )}
                  {searchResults.length === 0 && (
                    <div className="text-center text-green-300 py-4">
                      No emails found yet
                    </div>
                  )}
                </CardContent>
              </Card>
            </div>
          </TabsContent>

          {/* Generate Permutations Tab */}
          <TabsContent value="generate">
            <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
              <Card className="bg-gray-900 border border-blue-500">
                <CardHeader>
                  <CardTitle className="text-blue-400">Email Permutation Generator</CardTitle>
                  <CardDescription>Generate possible email combinations</CardDescription>
                </CardHeader>
                <CardContent className="space-y-4">
                  <div>
                    <Label htmlFor="firstName">First Name</Label>
                    <Input
                      id="firstName"
                      value={firstName}
                      onChange={(e) => setFirstName(e.target.value)}
                      placeholder="John"
                      className="bg-gray-800 border-blue-500 text-green-300"
                    />
                  </div>
                  
                  <div>
                    <Label htmlFor="lastName">Last Name</Label>
                    <Input
                      id="lastName"
                      value={lastName}
                      onChange={(e) => setLastName(e.target.value)}
                      placeholder="Doe"
                      className="bg-gray-800 border-blue-500 text-green-300"
                    />
                  </div>
                  
                  <div>
                    <Label htmlFor="company">Company Domain</Label>
                    <Input
                      id="company"
                      value={company}
                      onChange={(e) => setCompany(e.target.value)}
                      placeholder="example.com"
                      className="bg-gray-800 border-blue-500 text-green-300"
                    />
                  </div>
                  
                  <Button 
                    onClick={generateEmailPermutations}
                    className="bg-blue-600 hover:bg-blue-700 w-full"
                  >
                    <Users className="w-4 h-4 mr-2" />
                    Generate Permutations
                  </Button>
                </CardContent>
              </Card>

              <Card className="bg-gray-900 border border-blue-500">
                <CardHeader>
                  <CardTitle className="text-blue-400">Generated Emails</CardTitle>
                  <CardDescription>Possible email combinations</CardDescription>
                </CardHeader>
                <CardContent>
                  {searchResults.length > 0 && (
                    <div className="space-y-2 max-h-48 overflow-y-auto">
                      {searchResults.map((result, index) => (
                        <div key={index} className="flex justify-between items-center p-2 bg-gray-800 rounded">
                          <span className="text-green-300 font-mono text-sm">{result.email}</span>
                          <Badge className="bg-blue-600">Generated</Badge>
                        </div>
                      ))}
                    </div>
                  )}
                  {searchResults.length === 0 && (
                    <div className="text-center text-green-300 py-4">
                      Generate permutations to see results
                    </div>
                  )}
                </CardContent>
              </Card>
            </div>
          </TabsContent>

          {/* Bulk Extract Tab */}
          <TabsContent value="bulk">
            <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
              <Card className="bg-gray-900 border border-blue-500">
                <CardHeader>
                  <CardTitle className="text-blue-400">Bulk Email Extraction</CardTitle>
                  <CardDescription>Process multiple emails at once</CardDescription>
                </CardHeader>
                <CardContent className="space-y-4">
                  <div>
                    <Label htmlFor="bulkEmails">Email Addresses (one per line)</Label>
                    <Textarea
                      id="bulkEmails"
                      value={bulkEmails}
                      onChange={(e) => setBulkEmails(e.target.value)}
                      placeholder="john@example.com&#10;jane@company.com&#10;admin@site.org"
                      className="bg-gray-800 border-blue-500 text-green-300 font-mono"
                      rows={6}
                    />
                  </div>
                  
                  <div className="flex gap-2">
                    <Button 
                      onClick={extractBulk}
                      disabled={isLoading}
                      className="bg-blue-600 hover:bg-blue-700"
                    >
                      <Upload className="w-4 h-4 mr-2" />
                      Process Bulk
                    </Button>
                    
                    <Button 
                      onClick={() => setBulkEmails('')}
                      className="bg-gray-600 hover:bg-gray-700"
                    >
                      Clear
                    </Button>
                  </div>
                </CardContent>
              </Card>

              <Card className="bg-gray-900 border border-blue-500">
                <CardHeader>
                  <CardTitle className="text-blue-400">Bulk Statistics</CardTitle>
                  <CardDescription>Processing results</CardDescription>
                </CardHeader>
                <CardContent>
                  {statistics && (
                    <div className="space-y-2">
                      <div className="flex justify-between">
                        <span className="text-green-300">Total Processed:</span>
                        <Badge className="bg-blue-600">{statistics.total}</Badge>
                      </div>
                      <div className="flex justify-between">
                        <span className="text-green-300">Valid Emails:</span>
                        <Badge className="bg-green-600">{statistics.valid}</Badge>
                      </div>
                      <div className="flex justify-between">
                        <span className="text-green-300">Breached:</span>
                        <Badge className="bg-red-600">{statistics.breached}</Badge>
                      </div>
                      <div className="flex justify-between">
                        <span className="text-green-300">Social Profiles:</span>
                        <Badge className="bg-purple-600">{statistics.withSocialProfiles}</Badge>
                      </div>
                      <div className="flex justify-between">
                        <span className="text-green-300">Breach Rate:</span>
                        <span className="text-red-300">{statistics.breachRate}</span>
                      </div>
                    </div>
                  )}
                  {!statistics && (
                    <div className="text-center text-green-300 py-4">
                      Process emails to see statistics
                    </div>
                  )}
                </CardContent>
              </Card>
            </div>
          </TabsContent>

          {/* Breach Check Tab */}
          <TabsContent value="breach">
            <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
              <Card className="bg-gray-900 border border-blue-500">
                <CardHeader>
                  <CardTitle className="text-blue-400">Breach Database Check</CardTitle>
                  <CardDescription>Check if email appears in data breaches</CardDescription>
                </CardHeader>
                <CardContent className="space-y-4">
                  <div>
                    <Label htmlFor="breachEmail">Email Address</Label>
                    <Input
                      id="breachEmail"
                      value={email}
                      onChange={(e) => setEmail(e.target.value)}
                      placeholder="user@example.com"
                      className="bg-gray-800 border-blue-500 text-green-300"
                    />
                  </div>
                  
                  <Button 
                    onClick={checkBreachStatus}
                    disabled={isLoading}
                    className="bg-red-600 hover:bg-red-700 w-full"
                  >
                    <Shield className="w-4 h-4 mr-2" />
                    Check Breach Status
                  </Button>
                </CardContent>
              </Card>

              <Card className="bg-gray-900 border border-blue-500">
                <CardHeader>
                  <CardTitle className="text-blue-400">Breach Results</CardTitle>
                  <CardDescription>Security status</CardDescription>
                </CardHeader>
                <CardContent>
                  {breachResults && (
                    <div className="space-y-4">
                      <div className="flex justify-between">
                        <span className="text-green-300">Total Breaches:</span>
                        <Badge className={breachResults.breaches.length > 0 ? 'bg-red-600' : 'bg-green-600'}>
                          {breachResults.breaches.length}
                        </Badge>
                      </div>
                      
                      <div className="flex justify-between">
                        <span className="text-green-300">Pastes:</span>
                        <Badge className={breachResults.pastes.length > 0 ? 'bg-orange-600' : 'bg-green-600'}>
                          {breachResults.pastes.length}
                        </Badge>
                      </div>
                      
                      {breachResults.breaches.length > 0 && (
                        <div className="mt-4">
                          <h4 className="text-red-300 font-semibold mb-2">Breaches Found:</h4>
                          <div className="space-y-2 max-h-32 overflow-y-auto">
                            {breachResults.breaches.map((breach, index) => (
                              <div key={index} className="p-2 bg-red-900 rounded border border-red-500">
                                <div className="font-semibold text-red-300">{breach.name}</div>
                                <div className="text-xs text-red-400">{breach.date}</div>
                                <div className="text-xs text-gray-300 mt-1">
                                  Data: {breach.dataClasses.join(', ')}
                                </div>
                              </div>
                            ))}
                          </div>
                        </div>
                      )}
                    </div>
                  )}
                  {!breachResults && (
                    <div className="text-center text-green-300 py-4">
                      Check an email to see breach status
                    </div>
                  )}
                </CardContent>
              </Card>
            </div>
          </TabsContent>
        </Tabs>

        {/* Professional Profile Results */}
        {professionalResults && (
          <Card className="bg-gray-900 border border-blue-500 mt-6">
            <CardHeader>
              <CardTitle className="text-blue-400">Professional Profile</CardTitle>
              <CardDescription>Professional intelligence for {email}</CardDescription>
            </CardHeader>
            <CardContent>
              <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div className="space-y-4">
                  <div className="flex items-center gap-2">
                    <User className="w-4 h-4 text-blue-400" />
                    <div>
                      <div className="text-green-300 font-semibold">{professionalResults.name}</div>
                      <div className="text-xs text-gray-400">Full Name</div>
                    </div>
                  </div>
                  
                  <div className="flex items-center gap-2">
                    <Building className="w-4 h-4 text-blue-400" />
                    <div>
                      <div className="text-green-300">{professionalResults.company}</div>
                      <div className="text-xs text-gray-400">Company</div>
                    </div>
                  </div>
                  
                  <div className="flex items-center gap-2">
                    <Briefcase className="w-4 h-4 text-blue-400" />
                    <div>
                      <div className="text-green-300">{professionalResults.title}</div>
                      <div className="text-xs text-gray-400">Job Title</div>
                    </div>
                  </div>
                  
                  <div className="flex items-center gap-2">
                    <MapPin className="w-4 h-4 text-blue-400" />
                    <div>
                      <div className="text-green-300">{professionalResults.location}</div>
                      <div className="text-xs text-gray-400">Location</div>
                    </div>
                  </div>
                </div>
                
                <div>
                  <h4 className="text-blue-300 font-semibold mb-2">Skills</h4>
                  <div className="flex flex-wrap gap-2">
                    {professionalResults.skills?.slice(0, 8).map((skill, index) => (
                      <Badge key={index} className="bg-blue-600">
                        {skill}
                      </Badge>
                    ))}
                  </div>
                </div>
              </div>
            </CardContent>
          </Card>
        )}

        {/* Social Media Results */}
        {socialResults.length > 0 && (
          <Card className="bg-gray-900 border border-blue-500 mt-6">
            <CardHeader>
              <CardTitle className="text-blue-400">Social Media Profiles</CardTitle>
              <CardDescription>Social media intelligence for {email}</CardDescription>
            </CardHeader>
            <CardContent>
              <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                {socialResults.map((profile, index) => (
                  <div key={index} className="p-4 bg-gray-800 rounded border border-blue-500">
                    <div className="flex items-center justify-between mb-2">
                      <div className="font-semibold text-blue-300">{profile.platform}</div>
                      {profile.verified && <Badge className="bg-blue-600">Verified</Badge>}
                    </div>
                    <div className="text-green-300">@{profile.username}</div>
                    <div className="text-xs text-gray-400 mt-1">{profile.bio}</div>
                    <div className="flex justify-between text-xs text-gray-300 mt-2">
                      <span>Followers: {profile.followers?.toLocaleString()}</span>
                      <span>Following: {profile.following?.toLocaleString()}</span>
                    </div>
                    <div className="text-xs text-gray-400 mt-1">
                      Last active: {new Date(profile.lastActive || '').toLocaleDateString()}
                    </div>
                  </div>
                ))}
              </div>
            </CardContent>
          </Card>
        )}

        {/* Console Output */}
        <Card className="bg-gray-900 border border-blue-500 mt-6">
          <CardHeader className="flex flex-row items-center justify-between">
            <div>
              <CardTitle className="text-blue-400">Console Output</CardTitle>
              <CardDescription>Real-time extraction logs</CardDescription>
            </div>
            <div className="flex gap-2">
              <Button
                size="sm"
                variant="outline"
                onClick={() => exportResults('json')}
                className="border-blue-500 text-blue-300 hover:bg-blue-900"
              >
                <Download className="w-4 h-4 mr-2" />
                Export JSON
              </Button>
              <Button
                size="sm"
                variant="outline"
                onClick={() => exportResults('csv')}
                className="border-blue-500 text-blue-300 hover:bg-blue-900"
              >
                <Download className="w-4 h-4 mr-2" />
                Export CSV
              </Button>
              <Button
                size="sm"
                variant="outline"
                onClick={() => exportResults('txt')}
                className="border-blue-500 text-blue-300 hover:bg-blue-900"
              >
                <Download className="w-4 h-4 mr-2" />
                Export TXT
              </Button>
              <Button
                size="sm"
                variant="outline"
                onClick={clearConsole}
                className="border-blue-500 text-blue-300 hover:bg-blue-900"
              >
                Clear
              </Button>
            </div>
          </CardHeader>
          <CardContent>
            <div className="bg-black p-4 rounded border border-blue-500 font-mono text-sm max-h-64 overflow-y-auto">
              {consoleOutput.map((line, index) => (
                <div key={index} className="text-green-300 whitespace-pre-wrap">
                  {line}
                </div>
              ))}
              {consoleOutput.length === 0 && (
                <div className="text-green-500 text-center">
                  Console ready. Execute commands to see output.
                </div>
              )}
            </div>
          </CardContent>
        </Card>
      </div>
    </div>
  );
}