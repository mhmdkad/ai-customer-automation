from typing import Literal

from fastapi import Depends, FastAPI
from fastapi.responses import JSONResponse

from api_client import SuiteCRMClient
from pydantic import BaseModel, EmailStr
from services.support_case_service import SupportCaseService

from config import (
    SUITECRM_BASE_URL,
    SUITECRM_CLIENT_ID,
    SUITECRM_CLIENT_SECRET,
)

from exceptions import CRMServiceError


app = FastAPI()

@app.exception_handler(CRMServiceError)
def crm_service_error_handler(request, exc):
    return JSONResponse(
        status_code=503,
        content={"detail": str(exc)},
    )


class SupportCaseInput(BaseModel):
    customer_email: EmailStr
    message: str
    category: Literal["billing", "technical", "account", "other"]
    priority: Literal["low", "normal", "high"]

def get_support_case_service():
    return SupportCaseService(
        SuiteCRMClient(
            SUITECRM_BASE_URL,
            SUITECRM_CLIENT_ID,
            SUITECRM_CLIENT_SECRET,
        )
    )

@app.get("/health")
def health():
    return {"status": "ok"}


@app.post("/support-cases")
def create_support_case(
    input: SupportCaseInput,
    service: SupportCaseService = Depends(get_support_case_service),
):
    return service.create_support_case(
        customer_email=input.customer_email,
        message=input.message,
        category=input.category,
        priority=input.priority,
    )
