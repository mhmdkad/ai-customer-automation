import time

import requests


def request_with_retry(request_func, max_retries=3):
    for attempt in range(max_retries):
        try:
            return request_func()
        except (requests.Timeout, requests.ConnectionError):
            if attempt == max_retries - 1:
                raise

            time.sleep(2 ** attempt)
