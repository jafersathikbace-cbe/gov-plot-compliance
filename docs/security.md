# Security checklist

- Keep `.env` outside version control.
- Use strong application keys and secret storage in deployed environments.
- Restrict MongoDB network access and credentials.
- Enable HTTPS in production.
- Review Spatie permission assignments before creating production users.
- Keep uploaded evidence outside public web roots unless a controlled download endpoint is used.
- Review audit-log retention and access requirements with the system owner.
- Do not treat application configuration as a replacement for statutory or policy review.
