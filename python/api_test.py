import requests

support_request = {
    "customerEmail": "python@example.com",
    "message": "I cannot log into my account"
}

response = requests.post(
    "http://127.0.0.1:8000/api/support-requests",
    json=support_request,
    headers={
        "Authorization": "Bearer my-development-secret"
    }
)

response.raise_for_status()

print(response.status_code)
print(response.json())
