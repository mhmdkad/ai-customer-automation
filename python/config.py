import os

from dotenv import load_dotenv

load_dotenv()


def required_env(name: str) -> str:
    value = os.getenv(name)

    if not value:
        raise RuntimeError(f"Missing required environment variable: {name}")

    return value


SUITECRM_BASE_URL = required_env("SUITECRM_BASE_URL")
SUITECRM_CLIENT_ID = required_env("SUITECRM_CLIENT_ID")
SUITECRM_CLIENT_SECRET = required_env("SUITECRM_CLIENT_SECRET")
