import http from 'k6/http';
import { check, sleep } from 'k6';

export const options = {
  stages: [
    { duration: '10s', target: 20 },   // Naik ke 20 user dalam 10 detik
    { duration: '15s', target: 100 },  // Naik ke 100 user dalam 15 detik
    { duration: '30s', target: 100 },  // Tahan 100 user selama 30 detik
    { duration: '10s', target: 0 },    // Turun ke 0 user
  ],
};

export default function () {
  const url = 'http://127.0.0.1:8000/api/auth/login'; // ganti sesuai IP Laravel WSL kamu

  const payload = JSON.stringify({
    email: 'ryand@example.com',
    password: 'password123'
  });

  const params = {
    headers: {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
    },
  };

  const res = http.post(url, payload, params);

  check(res, {
    'status is 200': (r) => r.status === 200,
    'has token': (r) => r.json('token') !== undefined,
  });

  sleep(1);
}
