class SupportCaseService:

    def __init__(self, suitecrm_client):
        self.suitecrm_client = suitecrm_client

    def create_support_case(
        self,
        customer_email: str,
        message: str,
        category: str,
        priority: str,
    ):
        return self.suitecrm_client.create_record(
            "Cases",
            {
                "name": f"{category} - {customer_email}",
                "description": message,
                "priority": priority.capitalize(),
                "status": "New",
            },
        )
