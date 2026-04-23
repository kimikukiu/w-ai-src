📥 WhatsApp Web Group Member Extractor – Selenium Automation
This Python project automates the extraction of active and past members' phone numbers from a WhatsApp group using Selenium WebDriver and browser automation techniques.

📌 Features
Automates login via QR code scanning.

Searches and opens a specific WhatsApp group by name.

Navigates to Group Info.

Scrolls through the members list to extract:

✅ Active members

🕓 Past members (if any)

Extracts phone numbers using regex and saves them to:

active_members.txt

past_members.txt

Logs all actions in output.log.

🛠️ Requirements
Python 3.8+

Google Chrome browser

ChromeDriver (matching your Chrome version)

📦 Installation
Clone the repository or copy the script files into a directory.

Install required libraries:

bash
Copy
Edit
pip install selenium
📝 Configuration File – config.txt
This file should contain the following 3 lines:

php-template
Copy
Edit
<path_to_chromedriver>
https://web.whatsapp.com
<exact_group_name>
Example:

makefile
Copy
Edit
C:\Drivers\chromedriver.exe
https://web.whatsapp.com
My Family Group
📂 Required Files
config.txt – Configuration with driver path and group name

active_members.txt – Output file for current members

past_members.txt – Output file for past members

output.log – Execution logs for debugging/tracking

▶️ How to Run
bash
Copy
Edit
python whatsapp_group_member_extractor.py
Steps:

Browser opens and navigates to WhatsApp Web.

Scan the QR code manually to log in.

Script waits for messages to load.

Enters group name into the search bar.

Opens Group Info and extracts:

Current members

If available, past members (after clicking "View past members")

📤 Output Files
active_members.txt – List of active phone numbers

past_members.txt – List of removed/past phone numbers

Numbers are indexed like:

markdown
Copy
Edit
1. +91 98765 43210
2. +91 12345 67890
📌 Logging
All actions are logged into output.log with timestamps and error messages (if any).

⚠️ Notes & Limitations
This automation works only on desktop version of WhatsApp Web.

It depends on WhatsApp Web’s current UI structure. If WhatsApp changes its UI, the XPaths may break.

You must scan the QR code manually (no auto login).

This script cannot access phone numbers saved with names in your contact list—it only extracts visible phone numbers.

🤝 Credits
This project uses:

Selenium WebDriver

Regex for phone number parsing

Dynamic scrolling and XPath techniques

📬 Questions or Issues?
Open an issue or reach out if you need help customizing the script!

