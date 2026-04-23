from selenium import webdriver
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.chrome.service import Service
from selenium.webdriver.common.by import By
from selenium.webdriver.support import expected_conditions as EC
from selenium.webdriver.common.action_chains import ActionChains
import time
import re
import logging

# Logging setup
logging.basicConfig(
    level=logging.INFO,
    format='%(asctime)s - %(levelname)s - %(message)s',
    handlers=[
        logging.FileHandler("output.log", mode='w'),
        logging.StreamHandler()
    ]
)

# Config file read
with open("config.txt", "r") as file:
    lines = file.readlines()
    chromedriver_path = lines[0].strip()
    login_url = lines[1].strip()
    grp_name = lines[2].strip()

logging.info("Starting Chrome browser...")

# Launch Chrome
service = Service(chromedriver_path)
driver = webdriver.Chrome(service=service)
driver.get(login_url)
driver.maximize_window()

logging.info("Please scan the QR code to login.")
time.sleep(60)  # Wait for manual login

try:
    logging.info("Waiting for WhatsApp to finish loading messages...")
    WebDriverWait(driver, 30).until(
        EC.presence_of_element_located((By.XPATH, "//div[@role='textbox']"))
    )
    logging.info("WhatsApp loaded successfully.")

    # 👉 Search bar logic here
    search_xpath = "//div[@contenteditable='true'][@data-tab='3']"
    search_box = WebDriverWait(driver, 20).until(
        EC.presence_of_element_located((By.XPATH, search_xpath))
    )
    search_box.clear()
    search_box.send_keys(grp_name)
    time.sleep(3)

    chat_xpath = f"//span[contains(translate(@title, 'ABCDEFGHIJKLMNOPQRSTUVWXYZ', 'abcdefghijklmnopqrstuvwxyz'), '{grp_name.lower()}')]"
    chat_to_click = WebDriverWait(driver, 20).until(
        EC.element_to_be_clickable((By.XPATH, chat_xpath))
    )
    chat_to_click.click()
    logging.info(f"Clicked on chat: {grp_name}")
    time.sleep(10)

except Exception as e:
    logging.warning(f"Error: {e}")
    logging.info("Waiting 10 seconds before closing...")
    time.sleep(10)

menu = driver.find_element(By.XPATH, "//span[@class='xr9ek0c']")
menu.click()
time.sleep(5)
logging.info("Menu opened.")

dropdown = driver.find_element(By.XPATH, "//div[@aria-label='Group info']")
dropdown.click()
time.sleep(5)
logging.info("Dropdown opened.")

element = driver.find_element(By.CSS_SELECTOR, ".x1n2onr6.xyw6214.x78zum5.x1r8uery.x1iyjqo2.xdt5ytf.x6ikm8r.x1odjw0f.x1hc1fzr")
driver.execute_script("arguments[0].scrollTo(0, arguments[0].scrollHeight);", element)
time.sleep(5)
logging.info("Scrolled to the bottom of the group info.")

view_all = WebDriverWait(driver, 30).until(
    EC.element_to_be_clickable((By.CSS_SELECTOR, ".x1c4vz4f.xs83m0k.xdl72j9.x1g77sc7.xeuugli.x2lwn1j.xozqiw3.xqcrz7y.x12fk4p8.x1n68mz9"))
)
view_all.click()
time.sleep(5)
logging.info("Clicked on 'View All' button.")

# Function to extract numbers to specific file
def extract_numbers(driver, height_px, output_file, mode="w", start_counter=1):
    output_list = []
    counter = start_counter

    for y in range(0, height_px, 72):
        xpath = f"//div[contains(@style, 'transform: translateY({y}px)')]"
        try:
            b = driver.find_element(By.XPATH, xpath)
            text = b.text

            driver.execute_script("arguments[0].scrollIntoView();", b)
            time.sleep(0.2)

            phone_match = re.search(r'\+\d[\d\s]+', text)
            if phone_match:
                phone_number = phone_match.group(0).strip()
                output_list.append(f"{counter}. {phone_number}")
                counter += 1

        except:
            continue

    with open(output_file, mode, encoding="utf-8") as f:
        for line in output_list:
            f.write(line + "\n")

    logging.info(f"Extracted {len(output_list)} numbers to {output_file}")
    return counter

# Step 1: Get current members height
a = driver.find_element(By.XPATH, "//div[contains(@class, 'x1y332i5') and contains(@style, 'height:')]")
style = a.get_attribute("style")
match = re.search(r'height:\s*(\d+)px', style)
if match:
    height_px = int(match.group(1))
    logging.info("Dynamic Height (px): %s", height_px)

# Step 2: Extract current members to activemembers.txt
counter = extract_numbers(driver, height_px, output_file="active_members.txt", mode="w", start_counter=1)

# Step 3: Click 'View past members'
try:
    view_past_btn = WebDriverWait(driver, 10).until(
        EC.element_to_be_clickable((By.XPATH, "//button[normalize-space()='View past members']"))
    )
    view_past_btn.click()
    logging.info("Clicked 'View past members' button")
    time.sleep(3)

    # Step 4: Get new height for past members
    a = driver.find_element(By.XPATH, "//div[contains(@class, 'x1y332i5') and contains(@style, 'height:')]")
    style = a.get_attribute("style")
    match = re.search(r'height:\s*(\d+)px', style)
    if match:
        height_px = int(match.group(1))
        logging.info("Dynamic Height (px) for past members: %s", height_px)

    # Step 5: Extract past members to pastmembers.txt
    extract_numbers(driver, height_px, output_file="past_members.txt", mode="w", start_counter=counter)

except Exception as e:
    logging.warning("Couldn't find or click 'View past members': %s", str(e))

driver.quit()
logging.info("Browser closed.")
