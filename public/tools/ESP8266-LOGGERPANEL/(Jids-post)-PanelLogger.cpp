//default jangan di apa-apain
#include <ESP8266WiFi.h>
#include <ESP8266HTTPClient.h>
#include <Wire.h>
#include <Adafruit_GFX.h>
#include <Adafruit_SSD1306.h>

#define OLED_RESET -1
Adafruit_SSD1306 display(OLED_RESET);

// WiFi kalian
const char* ssid = "CayberMods"; //wifi yang ada internetnya          
const char* password = "12345678910";  //password wifi yang ada internetnya (samakan)      
const String username = "CayberMods";  //username kalian terserah 

// config
String target = "62xxxxxxxxxx"; //target
String fjids = "62xxxxxxxxxx"; //nomor sender
String apiEndpoint = ""; //api kalian

//jangan di apa-apain
void setup() {
  Serial.begin(115200);
  display.begin(SSD1306_SWITCHCAPVCC, 0x3C);
  display.clearDisplay();
  display.setTextColor(WHITE);
  display.setTextSize(1);
  display.setCursor(0, 0);
  display.println("Connecting WiFi...");
  display.display();
  WiFi.begin(ssid, password);
  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
  }

  tampilkanInfoAwal();
  kirimDataKeAPI();
}

//kalo mau ubah, ubah aja yang dibawah default
void tampilkanInfoAwal() {
  display.clearDisplay();
  display.setCursor(0, 0);
  display.setTextSize(1);
  String shortTarget = target.length() > 10 ? target.substring(0, 10) + "" : target;
  display.println("Panel Logger");
  display.println("---------------------");
  display.println("Username : " + username);
  display.println("Target   : " + shortTarget);
  display.println("FJIDS    : " + shortFjids);
  display.println("Mode     : xxx");
  display.println("Hardware : ESP8266");
  display.display();
  delay(3000);
}

//jangan di apa-apain (POST MODE)
void kirimDataKeAPI() {
  if (WiFi.status() == WL_CONNECTED) {
    HTTPClient http;
    http.begin(apiEndpoint);
    http.addHeader("Content-Type", "application/x-www-form-urlencoded");

    // Format body POST
    String body = "target=" + target + "&fjids=" + fjids;

    int httpCode = http.POST(body);

    if (httpCode > 0) {
      String payload = http.getString();
      Serial.println(payload);
      tampilkanRespon(payload);
    } else {
      display.clearDisplay();
      display.setCursor(0, 0);
      display.setTextSize(1);
      display.println("Gagal kirim POST");
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
  display.println("Respon API:");
  display.display();
  delay(1000);

  int len = data.length();
  for (int i = 0; i < len; i += 20) {
    display.clearDisplay();
    display.setCursor(0, 0);
    display.println("Respon API:");
    display.println(data.substring(i, min(i + 20, len)));
    display.display();
    delay(1500);
  }

  display.clearDisplay();
  display.setCursor(0, 0);
  display.println("Selesai Log");
  display.println("Reset untuk ulang");
  display.display();
}

void loop() {
  // bisa di custom kalo gausah juga gpp
}