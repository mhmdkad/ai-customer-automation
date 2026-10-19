from unittest.mock import Mock, patch

from api_client import SupportApiClient


@patch("api_client.requests.post")
def test_create_request_uses_timeout(mock_post):
    mock_response = Mock()
    mock_response.json.return_value = {"id": 1}
    mock_post.return_value = mock_response

    client = SupportApiClient(
        base_url="http://symfony.test",
        api_key="test-key",
    )

    result = client.create_request(
        customer_email="test@example.com",
        message="I cannot log in",
    )

    assert result == {"id": 1}
    mock_post.assert_called_once()

    _, kwargs = mock_post.call_args
    assert kwargs["timeout"] == 10
