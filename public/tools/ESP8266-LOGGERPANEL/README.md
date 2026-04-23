# 📱 ESP8266 WhatsApp Logger Panel  

![LoggerPanel](./thumbnail/thumbnail.jpg)  

**PanelLogger is a system for sending WhatsApp bugs automatically through ESP8266 by utilizing the API WhatsApp bugs  that use target parameters (recipient number) and fjids (sender number). The process starts with connecting to WiFi, then the ESP accesses the API URL and sends an HTTP GET/HTTP POST request containing the target and jids data. During the process, the status such as connection, sending, and server response is displayed directly on the 128x64 OLED display. If the sending is successful, a success notification will appear along with the response, while if it fails, an error message will be displayed. In essence, this is an IoT-based system for sending bugs through API.**  

---

## 🚨 **Disclaimer**  
⚠️ **For Educational & Security Testing Purposes Only!**  
This tool is designed for **legitimate penetration testing** on **your own systems**.  
🚫 **DO NOT USE** for illegal activities (spam, fraud, unauthorized access).  
🔐 **Developers are not responsible** for misuse. **Basic IoT & HTTP API knowledge required.**  

---

## 🌟 **Key Features**  
✅ **WhatsApp Bug Sender** via ESP8266 + API  
🖥 **Real-time OLED Display** (SSD1306 128x64)  
🌐 **Web UI Config** (Set Target/Fjids via ESP hotspot)  
📡 **Dual Mode**: WiFi Client + Fallback AP  
🔒 **Supports HTTP POST** (Secure API calls)  
⚡ **One-Click Resend** with Physical Button  

---

## 🛠 **Hardware Requirements**  
| Component | Notes |  
|-----------|-------|  
| **ESP8266** | NodeMCU/Wemos D1 Mini/LOLIN |  
| **OLED SSD1306** | I2C 128x64 Display |  
| **Micro USB Cable** | Must support data transfer |  
| **Breadboard** | Optional for prototyping |  

---

## 📚 **Libraries Needed**  
```cpp
#include <ESP8266WiFi.h>
#include <ESP8266HTTPClient.h>
#include <ESP8266WebServer.h>
#include <WiFiClient.h>
#include <Wire.h>
#include <Adafruit_GFX.h>
#include <Adafruit_SSD1306.h>
```
*(Install via Arduino IDE Library Manager)*  

---

## 🔌 **API Endpoint Examples**  
### **GET Method**  
```
http://your-api.com/api/bug?target=62XXXX&fjids=62XXXX
```  
### **POST Method (Recommended)**  
**Endpoint**:  
```
http://your-api.com/api/bug  
```  
**JSON Payload**:  
```json
{
  "target": "62XXXXXX",
  "fjids": "62XXXXXX"
}
```

---

## 🖥 **Web UI Access**  
1. **If WiFi fails**, ESP becomes hotspot (`192.168.4.1`)  
2. **Configure**:  
   - 📞 Target Number  
   - 📱 Sender Number (Fjids)  
   - 🔘 Trigger Bug Send  

---

## 🔄 **System Workflow**  
1. **Boot** → Attempt WiFi connection  
2. **Fallback** → AP mode if WiFi fails  
3. **User Input** → Set params via Web UI  
4. **API Call** → Send GET/POST request  
5. **OLED Feedback** → Success/Error logs  

---

## 🎨 **Customization Tips**  
- 🔧 Modify `apiUrl` for your API  
- 🎨 Redesign Web UI theme  
- ➕ Add fields (bug type, captcha, etc.)  

---

## 👨‍💻 **Developer Notes**  
🔧 **Not for beginners!** Requires understanding of:  
- HTTP Requests  
- ESP8266 WiFi/AP Modes  
- I2C OLED Communication  
- API Integration  

---

## 📜 **License**  
**Educational Use Only** • Made with ❤️ by **CayberMods**  

