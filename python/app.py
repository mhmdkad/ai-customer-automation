import os

from dotenv import load_dotenv
from fastapi import FastAPI
from api_client import SuiteCRMClient
from pydantic import BaseModel

class SupportCaseInput(BaseModel):
    customer_email: str
    message: str
    category: str
    priority: str

load_dotenv()

app = FastAPI()

suitecrm = SuiteCRMClient(
    os.getenv("SUITECRM_BASE_URL"),
    os.getenv("SUITECRM_CLIENT_ID"),
    os.getenv("SUITECRM_CLIENT_SECRET"),
)


@app.get("/health")
def health():
    return {"status": "ok"}


@app.post("/support-cases")
def create_support_case(input: SupportCaseInput):
    return suitecrm.create_record(
        "Cases",
        {
            "name": f"{input.category} - {input.customer_email}",
            "description": input.message,
            "priority": input.priority.capitalize(),
            "status": "New",
        },
    )
