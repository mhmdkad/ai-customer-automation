import requests


class SupportApiClient:

    def __init__(self, base_url, api_key):
        self.base_url = base_url
        self.api_key = api_key

    def create_request(self, customer_email, message):
        response = requests.post(
            f"{self.base_url}/api/support-requests",
            json={
                "customerEmail": customer_email,
                "message": message,
            },
            headers={
                "Authorization": f"Bearer {self.api_key}",
            },
        )

        response.raise_for_status()

        return response.json()

    def get_request(self, request_id):
        response = requests.get(
            f"{self.base_url}/api/support-requests/{request_id}",
            headers={
                "Authorization": f"Bearer {self.api_key}",
            },
        )

        response.raise_for_status()

        return response.json()

    def list_requests(self, status=None):
        response = requests.get(
            f"{self.base_url}/api/support-requests",
            params={"status": status} if status else {},
            headers={
                "Authorization": f"Bearer {self.api_key}",
            },
        )

        response.raise_for_status()

        return response.json()
