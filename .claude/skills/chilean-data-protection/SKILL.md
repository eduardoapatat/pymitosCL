---
name: chilean-data-protection
description: 'ACTIVATE whenever code touches personal data in a Chilean context. This includes RUT, names, email, phone, address, or any contact data of people or companies; creating or changing migrations/models/forms/exports that store, display, transfer, or delete such data; writing logs that might contain personal data; building data exports (Excel/PDF/API); automated decisions or profiling; and any auditing, consent, or data-retention work. Activate when the user mentions ley de proteccion de datos, datos personales, datos sensibles, privacidad, RUT como dato personal, Ley 19.628, Ley 21.719, anonimizacion, consentimiento, retencion de datos, ARSOP/ARCO rights, or logging of business operations. This skill encodes the obligations of Chiles Ley 21.719 (in force from 1 December 2026, replacing Ley 19.628) as practical engineering rules. Do NOT activate for purely presentational UI work with no personal data, or backend logic that handles no personal/contact information.'
metadata:
  author: reusable
---

# Chilean Data Protection (Ley 21.719) — Engineering Rules

Ley 21.719 (published 13 Dec 2024, in force from **1 December 2026**) fully replaces Ley 19.628 and creates the Data Protection Agency (APDP). This skill turns that law into practical rules to follow while writing code. It is written to be reusable across projects.

Non-compliance is expensive: fines scale from minor (up to 5,000 UTM), serious (up to 10,000 UTM) to very serious (up to 20,000 UTM), and on repeat offenses up to 4% of annual Chilean revenue. The APDP can also suspend or prohibit processing. Build to comply from day one.

## Key definitions

- **Personal data**: any information linked to an identified or identifiable natural person. In Chile the RUT is personal data.
- **Sensitive personal data**: health, biometrics, ethnic origin, religious/political beliefs, sexual life, sexual orientation, gender identity. Higher protection; avoid storing unless strictly required.
- **Data processing**: any operation on personal data — collection, storage, communication, transmission, use, deletion.
- **Data subject (titular)**: the natural person the data belongs to.
- **Data controller (responsable)**: who decides the purposes and means of processing.

## Core principles (apply by default in every design)

1. **Lawfulness & purpose**: every personal data field must have a declared, legitimate purpose. Do not reuse data for unrelated purposes.
2. **Proportionality / minimization**: store only what the purpose requires. No personal fields "just in case".
3. **Quality**: keep data accurate and up to date; allow rectification.
4. **Security**: protect data by design — access controls, encryption at rest/in transit, tenant/scope isolation so one party never sees anothers data.
5. **Transparency**: be able to tell the subject what is processed, why, on what legal basis, and for how long.
6. **Accountability**: be able to demonstrate compliance (auditable records of processing).

## Legal basis for processing (every personal-data use needs one)

Each processing of personal data must rest on a valid legal basis. The main ones:

- **Consent** — the general rule. Must be free, informed, specific, unequivocal, prior, revocable, and **verifiable/demonstrable**. Tacit or pre-checked consent is NOT valid.
- **Contract**, **legal obligation**, **exercising rights before courts**, **economic-obligation data**.
- **Legitimate interest** — still a valid basis, but NOT the loose pre-2026 version. It now requires a **documented balancing test** (the controller's interest vs. the subject's rights), and the subject can always demand to know the legitimate interest relied on. Do not treat it as a default catch-all.

The controller must be able to **prove** the legal basis for any processing.

## Consent — how to store it (when consent is the basis)

When consent is the legal basis, persist it so it is auditable:

- A **timestamp** of when consent was given.
- The **version of the terms/policy** accepted.
- The **identity/session/IP** of who gave it.
- A way to record **withdrawal** of consent (consent is revocable at any time, and revocation must be as easy as giving it).

## Data subject rights (ARSOP) — the data model must support them

Access, Rectification, Suppression (deletion), Opposition, Portability — plus the right to withdraw consent and to challenge automated decisions. Practically:

- Model must allow **reading** all personal data held about a subject.
- Model must allow **rectifying** and **deleting/anonymizing** a subjects data without breaking the integrity of legally retained records (e.g. issued invoices).
- Provide a path to **export** a subjects data (portability) and to **revoke consent**.

## Engineering rules (what to actually do in code)

- **Catalog the data**: for each personal field, know its type, subject, purpose, legal basis, recipients, source, and retention period. Document this in domain docs (not in code comments).
- **Logging**: never log personal data in clear text (no full RUT, email, name). Log internal identifiers (`*_id`) and masked values when needed (e.g. RUT `12.***.**8-9`).
- **Access audit log**: to prove accountability, record WHO accessed/exported WHICH personal data and when (e.g. "user X exported the client list", "user Y viewed cliente_id Z") — log the actor and the target id, never the personal data itself. This is separate from masking: masking hides the data, the audit log proves access control.
- **Exports**: include only the fields the export legally requires; never dump full tables of personal data unfiltered. Every export should produce an access-audit entry.
- **Retention**: distinguish data with a legal retention obligation (e.g. tax documents — do not freely delete) from contact data that can be anonymized once no longer needed.
- **Anonymization vs pseudonymization (do not confuse them)**:
  - **Pseudonymization** = data can still be re-identified with extra info. A hash of a RUT (MD5/SHA256) is only pseudonymization — the RUT space is small and brute-forceable, so a hash does NOT make it anonymous. Useful internally but still treated as personal data.
  - **Anonymization** = irreversible. The original value is destroyed and replaced by a random UUID or a generic neutral value, so the subject can never be re-identified. Only truly anonymized data falls outside the law.
  - When fulfilling a deletion/suppression right, anonymize (don't just hash) while keeping references to legally retained records intact.
- **Sensitive data**: avoid collecting it; if unavoidable, isolate it, restrict access, and encrypt.
- **Automated decisions / profiling**: if logic profiles or auto-decides about a person, make it explainable and contestable.
- **Third parties / cloud / international transfers**: track which providers and which regions process the data; ensure adequate safeguards.

## Minimum compliance checklist (verify these exist for the system)

1. Data processing policy. 2. Identified controller + contact channel. 3. Information on what is processed and why. 4. Security measures. 5. Procedure to exercise ARSOP rights. 6. Right to complain to the Agency. 7. International transfers handling. 8. Retention periods. 9. Data origin. 10. Right to withdraw consent. 11. Disclosure of automated decisions.

## Project-specific notes

(Each project completes this: exact retention periods, which fields are masked, consent requirements, and any additional obligations.)
