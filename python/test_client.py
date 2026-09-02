import os
import requests

from dotenv import load_dotenv

from api_client import SupportApiClient


load_dotenv()

client = SupportApiClient(
    os.getenv("API_BASE_URL"),
    os.getenv("API_KEY"),
)

result = client.create_request(
    "env-test@example.com",
    "Testing environment variables",
)

try:
    result = client.list_requests("new")
    print(result)

except requests.exceptions.HTTPError as error:
    print("API request failed:", error)
