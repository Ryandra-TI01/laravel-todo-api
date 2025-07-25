import http from 'k6/http';
import { check, sleep } from 'k6';

export const options = {
  stages: [
    { duration: '5s', target: 20 },
    { duration: '10s', target: 50 },
    { duration: '20s', target: 100 },
    { duration: '5s', target: 0 },
  ],
};

export default function () {
  const userId = Math.floor(Math.random() * 100) + 1; // Get user ID between 1 and 100

  const res = http.get(`http://127.0.0.1:8000/api/test-tasks/${userId}`, {
    headers: { Accept: 'application/json' },
  });

  check(res, {
    'status is 200': (r) => r.status === 200,
    'has data': (r) => Array.isArray(r.json().data),
  });

  sleep(1);
}
