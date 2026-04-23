from colorama import Fore, Style, init

init(autoreset=True)

def show_banner():
    """Display the tool banner with enhanced colors"""
    banner = f"""
{Fore.MAGENTA}
=================================================

{Fore.RED}{Style.BRIGHT}
██╗   ██╗ ███████╗ ███████╗
╚██╗ ██╔╝ ██╔════╝ ██╔════╝
 ╚████╔╝  ███████╗ ███████╗
 ╚████╔╝  ╚════██║ ╚════██║
╔██╗ ██╗  ███████║ ███████║
██╔╝ ╚██╗ ╚══════╝ ╚══════╝

{Fore.WHITE}{Style.BRIGHT}XSS SCANNER {Fore.YELLOW}v1
{Fore.LIGHTWHITE_EX}Team BD Cyber Ninja{Style.RESET_ALL}

{Fore.CYAN}by Xit-eXploiteR{Style.RESET_ALL}

{Fore.BLUE}{Style.BRIGHT}Features:
{Fore.WHITE}• DOM-based XSS detection
{Fore.WHITE}• Reflected XSS detection
{Fore.WHITE}• WAF bypass techniques
{Fore.WHITE}• Smart payload generation
{Fore.WHITE}• Comprehensive reporting{Style.RESET_ALL}

{Fore.MAGENTA}=================================================
"""
    print(banner)
