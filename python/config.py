import os

from dotenv import load_dotenv

load_dotenv()


SUITECRM_BASE_URL = os.getenv("SUITECRM_BASE_URL")
SUITECRM_CLIENT_ID = os.getenv("SUITECRM_CLIENT_ID")
SUITECRM_CLIENT_SECRET = os.getenv("SUITECRM_CLIENT_SECRET")
