import os
import requests

from dotenv import load_dotenv

from api_client import SupportApiClient

from api_client import SuiteCRMClient


load_dotenv()

# client = SupportApiClient(
#     os.getenv("API_BASE_URL"),
#     os.getenv("API_KEY"),
# )

# result = client.create_request(
#     "env-test@example.com",
#     "Testing environment variables",
# )

# try:
#     result = client.list_requests("new")
#     print(result)

# except requests.exceptions.HTTPError as error:
#     print("API request failed:", error)


suitecrm = SuiteCRMClient(
    os.getenv("SUITECRM_BASE_URL"),
    os.getenv("SUITECRM_CLIENT_ID"),
    os.getenv("SUITECRM_CLIENT_SECRET"),
)

accounts = suitecrm.get_accounts()
print(accounts)

accounts = suitecrm.get_accounts()
print(accounts)

result = suitecrm.create_record(
    "Accounts",
    {
        "name": "Automation Demo Company",
    },
)

print(result)
