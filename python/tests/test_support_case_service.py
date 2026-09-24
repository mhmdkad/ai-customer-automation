from services.support_case_service import SupportCaseService


class FakeSuiteCRMClient:

    def __init__(self):
        self.created_record = None

    def create_record(self, module, attributes):
        self.created_record = {
            "module": module,
            "attributes": attributes,
        }

        return self.created_record


def test_create_support_case():
    client = FakeSuiteCRMClient()
    service = SupportCaseService(client)

    result = service.create_support_case(
        customer_email="test@example.com",
        message="I cannot log in",
        category="account",
        priority="high",
    )

    assert result["module"] == "Cases"
    assert result["attributes"]["name"] == "account - test@example.com"
    assert result["attributes"]["description"] == "I cannot log in"
    assert result["attributes"]["priority"] == "High"
    assert result["attributes"]["status"] == "New"


def test_create_support_case_propagates_crm_error():
    class FailingSuiteCRMClient:
        def create_record(self, module, attributes):
            raise RuntimeError("CRM unavailable")

    service = SupportCaseService(FailingSuiteCRMClient())

    try:
        service.create_support_case(
            customer_email="test@example.com",
            message="I cannot log in",
            category="account",
            priority="high",
        )
        assert False, "Expected RuntimeError"
    except RuntimeError as exc:
        assert str(exc) == "CRM unavailable"
