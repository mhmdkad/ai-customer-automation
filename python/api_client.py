import requests
import time
from http_client import request_with_retry
from exceptions import CRMServiceError
import logging

logger = logging.getLogger(__name__)

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
            timeout=10
        )

        response.raise_for_status()

        return response.json()

    def get_request(self, request_id):
        response = requests.get(
            f"{self.base_url}/api/support-requests/{request_id}",
            headers={
                "Authorization": f"Bearer {self.api_key}",
            },
            timeout=10
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
            timeout=10
        )

        response.raise_for_status()

        return response.json()


class SuiteCRMClient:

    def __init__(self, base_url, client_id, client_secret):
        self.base_url = base_url
        self.client_id = client_id
        self.client_secret = client_secret

        self.access_token = None
        self.token_expires_at = 0

    def get_access_token(self):
        if self.access_token and time.time() < self.token_expires_at:
            return self.access_token

        response = request_with_retry(
            lambda: requests.post(
                f"{self.base_url}/Api/access_token",
                data={
                    "grant_type": "client_credentials",
                },
                auth=(
                    self.client_id,
                    self.client_secret,
                ),
                timeout=10,
            )
        )

        response.raise_for_status()

        data = response.json()

        self.access_token = data["access_token"]
        self.token_expires_at = time.time() + data["expires_in"] - 60

        return self.access_token

    def get_accounts(self):
        token = self.get_access_token()

        response = requests.get(
            f"{self.base_url}/Api/V8/module/Accounts",
            headers={
                "Authorization": f"Bearer {token}",
                "Accept": "application/json",
            },
            timeout=10,
        )

        response.raise_for_status()

        return response.json()

    def create_record(self, module, attributes):
        logger.info("Creating SuiteCRM record: module=%s", module)

        token = self.get_access_token()

        response = requests.post(
            f"{self.base_url}/Api/V8/module",
            headers={
                "Authorization": f"Bearer {token}",
                "Content-Type": "application/vnd.api+json",
                "Accept": "application/json",
            },
            json={
                "data": {
                    "type": module,
                    "attributes": attributes,
                }
            },
            timeout=10,
        )

        try:
            response.raise_for_status()
        except requests.HTTPError as exc:
            logger.error(
                "SuiteCRM request failed: status=%s",
                response.status_code,
            )
            raise CRMServiceError(
                f"SuiteCRM request failed with status {response.status_code}"
            ) from exc

        return response.json()
