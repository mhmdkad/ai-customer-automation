from fastapi.testclient import TestClient

from app import app, get_support_case_service


class FakeSupportCaseService:
    def create_support_case(
        self,
        customer_email,
        message,
        category,
        priority,
    ):
        return {
            "id": "fake-case-1",
            "customer_email": customer_email,
            "message": message,
            "category": category,
            "priority": priority,
        }


app.dependency_overrides[get_support_case_service] = (
    lambda: FakeSupportCaseService()
)

client = TestClient(app)


def test_health():
    response = client.get("/health")

    assert response.status_code == 200
    assert response.json() == {"status": "ok"}


def test_create_support_case():
    response = client.post(
        "/support-cases",
        json={
            "customer_email": "test@example.com",
            "message": "I cannot log in",
            "category": "account",
            "priority": "high",
        },
    )

    assert response.status_code == 200

    data = response.json()

    assert data["id"] == "fake-case-1"
    assert data["category"] == "account"
    assert data["priority"] == "high"


def test_create_support_case_rejects_invalid_priority():
    response = client.post(
        "/support-cases",
        json={
            "customer_email": "test@example.com",
            "message": "I cannot log in",
            "category": "account",
            "priority": "super-important",
        },
    )

    assert response.status_code == 422
