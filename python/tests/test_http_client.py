from unittest.mock import Mock, call, patch

import pytest
import requests

from http_client import request_with_retry


@patch("http_client.time.sleep")
def test_retries_transient_errors_then_succeeds(mock_sleep):
    request = Mock(
        side_effect=[
            requests.ConnectionError("Connection lost"),
            requests.Timeout("Request timed out"),
            {"status": "ok"},
        ]
    )

    result = request_with_retry(request)

    assert result == {"status": "ok"}
    assert request.call_count == 3
    assert mock_sleep.call_args_list == [call(1), call(2)]


@patch("http_client.time.sleep")
def test_reraises_after_max_retries(mock_sleep):
    request = Mock(
        side_effect=requests.ConnectionError("Connection lost")
    )

    with pytest.raises(requests.ConnectionError):
        request_with_retry(request)

    assert request.call_count == 3
    assert mock_sleep.call_args_list == [call(1), call(2)]


@patch("http_client.time.sleep")
def test_does_not_retry_other_exceptions(mock_sleep):
    request = Mock(side_effect=ValueError("Invalid configuration"))

    with pytest.raises(ValueError, match="Invalid configuration"):
        request_with_retry(request)

    request.assert_called_once()
    mock_sleep.assert_not_called()
