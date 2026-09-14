import os
import requests
from dotenv import load_dotenv

load_dotenv()


def get_access_token():
    response = requests.post(
        "http://suitecrm.local/Api/access_token",
        data={
            "grant_type": "client_credentials",
        },
        auth=(
            os.getenv("SUITECRM_CLIENT_ID"),
            os.getenv("SUITECRM_CLIENT_SECRET"),
        ),
    )

    response.raise_for_status()

    return response.json()["access_token"]

token = get_access_token()

response = requests.get(
    "http://suitecrm.local/Api/V8/module/Accounts",
    headers={
        "Authorization": f"Bearer {token}",
        "Accept": "application/json",
    },
)

response.raise_for_status()

print(response.status_code)
print(response.text)
