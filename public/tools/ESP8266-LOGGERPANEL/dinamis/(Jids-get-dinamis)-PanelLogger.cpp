//jangan di apa-apain
#include <ESP8266WiFi.h>
#include <ESP8266HTTPClient.h>
#include <ESP8266WebServer.h>
#include <WiFiClient.h>
#include <Wire.h>
#include <Adafruit_GFX.h>
#include <Adafruit_SSD1306.h>

#define OLED_RESET -1
Adafruit_SSD1306 display(OLED_RESET);

// bisa custom
const char* default_ssid = "CayberMods";
const char* default_password = "12345678910";
String username = "CayberMods";
String target = "62xxxxxxxxxx";
String fjids = "62xxxxxxxxxx";
String apiUrl = "";

//jangan di apa-apain
ESP8266WebServer server(80);

// bisa custom 
const char* configPage = R"rawliteral(
<!DOCTYPE HTML>
<html>
<head>
  <title>ESP8266 Logger Configuration</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    .container { max-width: 500px; margin: 0 auto; }
    .form-group { margin-bottom: 15px; }
    label { display: block; margin-bottom: 5px; }
    input { width: 100%; padding: 8px; box-sizing: border-box; }
    button { background-color: #4CAF50; color: white; padding: 10px 15px; border: none; cursor: pointer; }
    button:hover { background-color: #45a049; }
  </style>
</head>
<body>
  <div class="container">
    <h2>Logger Configuration</h2>
    <form action="/save" method="post">
      <div class="form-group">
        <label for="ssid">WiFi SSID:</label>
        <input type="text" id="ssid" name="ssid" value="%SSID%" required>
      </div>
      <div class="form-group">
        <label for="password">WiFi Password:</label>
        <input type="password" id="password" name="password" value="%PASSWORD%" required>
      </div>
      <div class="form-group">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" value="%USERNAME%" required>
      </div>
      <div class="form-group">
        <label for="target">Target Number:</label>
        <input type="text" id="target" name="target" value="%TARGET%" required>
      </div>
      <div class="form-group">
        <label for="fjids">Sender Number (FJIDS):</label>
        <input type="text" id="fjids" name="fjids" value="%FJIDS%" required>
      </div>
      <div class="form-group">
        <label for="apiurl">API URL (leave {target} and {fjids} as placeholders):</label>
        <input type="text" id="apiurl" name="apiurl" value="%APIURL%" required>
      </div>
      <button type="submit">Save Configuration</button>
    </form>
  </div>
</body>
</html>
)rawliteral";

//jangan di apa-apain
void setup() {
  Serial.begin(115200);
  display.begin(SSD1306_SWITCHCAPVCC, 0x3C);
  display.clearDisplay();
  display.setTextColor(WHITE);
  display.setTextSize(1);
  display.setCursor(0, 0);
  display.println("Starting up...");
  display.display();
  loadConfig();
  connectToWiFi();  
  server.on("/", handleRoot);
  server.on("/save", handleSave);
  server.begin(); 
  tampilkanInfoAwal();  
  if (target != "62xxxxxxxxxx" && fjids != "62xxxxxxxxxx" && apiUrl != "") {
    ambilLogDariAPI();
  }
}

//jangan di apa-apain
void loop() {
  server.handleClient();
}

//jangan di apa-apain
void connectToWiFi() {
  display.clearDisplay();
  display.setCursor(0, 0);
  display.println("Connecting WiFi...");
  display.println("SSID: " + String(WiFi.SSID()));
  display.display();
  
  WiFi.begin(WiFi.SSID().c_str(), WiFi.psk().c_str());
  
  int attempts = 0;
  while (WiFi.status() != WL_CONNECTED && attempts < 20) {
    delay(500);
    Serial.print(".");
    attempts++;
  }
  
  if (WiFi.status() == WL_CONNECTED) {
    display.clearDisplay();
    display.setCursor(0, 0);
    display.println("WiFi Connected");
    display.println("IP: " + WiFi.localIP().toString());
    display.display();
  } else {
    startAPMode();
  }
}

//custom aja
void startAPMode() {
  WiFi.mode(WIFI_AP);
  WiFi.softAP("LoggerConfigAP", "config123");
  
  display.clearDisplay();
  display.setCursor(0, 0);
  display.println("AP Mode Active");
  display.println("SSID: LoggerConfigAP");
  display.println("Pass: config123");
  display.println("IP: 192.168.4.1");
  display.display();
}

//jangan di apa-apain
void handleRoot() {
  String page = configPage;
  page.replace("%SSID%", WiFi.SSID());
  page.replace("%PASSWORD%", WiFi.psk());
  page.replace("%USERNAME%", username);
  page.replace("%TARGET%", target);
  page.replace("%FJIDS%", fjids);
  page.replace("%APIURL%", apiUrl);
  
  server.send(200, "text/html", page);
}

//jangan di apa-apain
void handleSave() {
  if (server.method() != HTTP_POST) {
    server.send(405, "text/plain", "Method Not Allowed");
    return;
  }
  String newSSID = server.arg("ssid");
  String newPassword = server.arg("password");
  username = server.arg("username");
  target = server.arg("target");
  fjids = server.arg("fjids");
  apiUrl = server.arg("apiurl");
  saveConfig();
  server.send(200, "text/html", "<html><body><h1>Configuration Saved</h1><p>Device will restart to apply changes.</p></body></html>");
  delay(1000);
  ESP.restart();
}

void loadConfig() {
//custom aja
}

void saveConfig() {
//custom aja
}
//bisa custom
void tampilkanInfoAwal() {
  display.clearDisplay();
  display.setCursor(0, 0);
  display.setTextSize(1);
  String shortTarget = target.length() > 10 ? target.substring(0, 10) + "" : target;
  String shortFjids = fjids.length() > 10 ? fjids.substring(0, 10) + "" : fjids;
  display.println("Panel Logger");
  display.println("---------------------");
  display.println("Username: " + username);
  display.println("Target: " + shortTarget);
  display.println("Sender: " + shortFjids);
  display.println("IP: " + WiFi.localIP().toString());
  display.println("Mode: xxx");
  display.println("Hardware: ESP8266");
  display.display();
  delay(3000);
}

//jangan di apa-apain get mode
void ambilLogDariAPI() { 
  String finalUrl = apiUrl;
  finalUrl.replace("{target}", target);
  finalUrl.replace("{fjids}", fjids);
  
  if (WiFi.status() == WL_CONNECTED) {
    HTTPClient http;
    http.begin(finalUrl);
    int httpCode = http.GET();

    if (httpCode > 0) {
      String payload = http.getString();
      Serial.println(payload);
      tampilkanRespon(payload);
    } else {
      display.clearDisplay();
      display.setCursor(0, 0);
      display.setTextSize(1);
      display.println("Failed to call API");
      display.println("Code: " + String(httpCode));
      display.display();
    }

    http.end();
  }
}
//jangan di apa-apain
void tampilkanRespon(String data) {
  display.clearDisplay();
  display.setCursor(0, 0);
  display.setTextSize(1);
  display.println("API Response:");
  display.display();
  delay(1000);

  int len = data.length();
  for (int i = 0; i < len; i += 20) {
    display.clearDisplay();
    display.setCursor(0, 0);
    display.println("API Response:");
    display.println(data.substring(i, min(i + 20, len)));
    display.display();
    delay(1500);
  }

  display.clearDisplay();
  display.setCursor(0, 0);
  display.println("Log Complete");
  display.println("Reset to retry");
  display.println("or visit IP to");
  display.println("reconfigure");
  display.display();
}