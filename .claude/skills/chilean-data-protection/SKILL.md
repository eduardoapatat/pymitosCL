---
name: chilean-data-protection
description: 'ACTIVATE when code stores, displays, deletes, exports, or transfers personal data in a Chilean context: migrations/models/forms/Eloquent for fields like RUT, name, email, phone, or address of people or companies; data exports (Excel/PDF/API) containing such fields; deletion/anonymization flows; consent handling; data-retention rules; automated decisions or profiling about a person; or a data-breach notification flow. Also activate when the user mentions ley de proteccion de datos, datos personales, datos sensibles, RUT como dato personal, Ley 21.719, anonimizacion, seudonimizacion, consentimiento, retencion de datos, or ARSOP/ARCO rights. This skill encodes Chiles Ley 21.719 (in force 1 December 2026) as engineering rules. Do NOT activate for: UI-only work with no personal data; backend logic handling only internal identifiers (e.g. logging with cliente_id/user_id); or features that touch no personal/contact fields.'
metadata:
  author: reusable
---

# Chilean Data Protection (Ley 21.719) — Engineering Rules

Ley 21.719 (published 13 Dec 2024, in force from **1 December 2026**) fully replaces Ley 19.628 and creates the Data Protection Agency (APDP). This skill turns that law into practical rules to follow while writing code. It is written to be reusable across projects.

This is an engineering guide, not formal legal advice; for complex cases defer to a lawyer. Where the law is nuanced, prefer the safer default in code and flag the decision rather than over-claiming compliance.

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
4. **Security (privacy by design & by default, art. 14 quater/quinquies)**: protect data from the design stage and by default — access controls, encryption at rest/in transit, pseudonymization, tenant/scope isolation so one party never sees anothers data. Measures must be proportional to the risk.
5. **Transparency (art. 14 ter)**: be able to inform the subject (publicly, e.g. on the website) what is processed, why, on what legal basis, recipients, retention period, ARSOP rights, contact channel, and international transfers.
6. **Accountability (art. 14 a)**: be able to **prove** lawful processing to the APDP.

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
- The **identity, session information and/or IP address** of who gave it, where proportionate and lawful.
- A way to record **withdrawal** of consent (consent is revocable at any time, and revocation must be as easy as giving it).

## Data subject rights (ARSOP) — the data model must support them

Access, Rectification, Suppression (deletion), Opposition, Portability (also Blocking) — plus the right to withdraw consent and to challenge automated decisions. The subject must be able to exercise them via a simple channel, and the controller must respond within the statutory deadline (around 30 days per art. 10 — verify the deadline in force, as it may be adjusted by regulation). Practically:

- Model must allow **reading** all personal data held about a subject.
- Model must allow **rectifying** and **deleting/anonymizing** a subjects data without breaking the integrity of legally retained records (e.g. issued invoices).
- Provide a path to **export** a subjects data (portability) and to **revoke consent**.

## Engineering rules (what to actually do in code)

- **Transparency disclosure (art. 14 ter), not a literal "RAT"**: the law does NOT mandate an internal Registro de Actividades de Tratamiento by that name (that is a GDPR import). What it requires is to **publicly disclose** (e.g. on the website) the data policy, controller + contact, purposes, legal basis, security measures, retention periods, ARSOP rights, and international transfers. Keeping an internal catalog of these per field is good practice that makes the disclosure possible — document it (README/docs, not code comments).
- **Logging**: prefer internal identifiers (`user_id`, `cliente_id`, `empresa_id`) instead of direct identifiers such as RUT, email or name. These ids may still constitute indirect personal data for the controller (they resolve to a person via the DB). Keeping the mapping between internal ids and direct identifiers protected and separated reduces exposure and may support pseudonymization strategies, but does not automatically constitute legal pseudonymization. Do not put RUT, email or name in logs at all; no need to mask, they simply do not belong there.
- **Access audit log**: to prove accountability, record WHO accessed/exported WHICH personal data and when (e.g. "user X exported the client list", "user Y viewed cliente_id Z") — log the actor and the target id, never the personal data itself. This is separate from masking: masking hides the data, the audit log proves access control.
- **Exports**: include only the fields the export legally requires; never dump full tables of personal data unfiltered. Every export should produce an access-audit entry.
- **Retention**: distinguish data with a legal retention obligation (e.g. tax documents — do not freely delete) from contact data that can be anonymized once no longer needed.
- **Three levels — masking vs pseudonymization vs anonymization (do not confuse them)**:
  - **Masking / obfuscation** (weakest) = hiding part of a value for display, e.g. `12.***.**8-9`. Masking is not recognized as anonymization or legal pseudonymization and should not be relied upon as a standalone compliance control. For a RUT it is reconstructible (small space + visible verifier digit). **Use only for UI/presentation** (showing a partial RUT to a user); it may complement other measures but never replaces them. NEVER rely on it for logs or storage.
  - **Pseudonymization** (legal definition) = data can no longer be attributed to a subject **without additional information**, AND that additional information is **kept separate and protected** by technical/organizational measures. Note: a plain hash of a RUT is NOT proper pseudonymization — the RUT space is small and brute-forceable, and no protected "additional information" is kept separate. Pseudonymized data is still personal data.
  - **Anonymization** (strongest) = irreversible removal or transformation of identifiers such that re-identification is no longer reasonably possible. Note: replacing a value with a random UUID does NOT anonymize on its own if a correspondence table (uuid↔person) still exists — that is still personal data. Only truly anonymized data falls outside the law.
  - When fulfilling a suppression right, evaluate the correct action: delete, block, retain under a legal obligation (e.g. tax records), or irreversibly anonymize — do not mask or hash. See Retention above.
- **Sensitive data**: avoid collecting it; if unavoidable, isolate it, restrict access, and encrypt.
- **Automated decisions / profiling**: if logic profiles or auto-decides about a person, make it explainable and contestable.
- **Third parties / cloud / international transfers**: track which providers and which regions process the data; ensure adequate safeguards.
- **Data breaches (Art. 14 quinquies/sexies)**: real security measures are pseudonymization, encryption and restricted access (masking is not one). On a breach with reasonable risk to subjects, notify the APDP without undue delay (no fixed 72h deadline), keep an internal record of the incident and remediation, and notify affected subjects directly if sensitive/minors/financial data is involved.
- **DPO (Delegado de Proteccion de Datos, arts. 49-50)**: the law establishes cases where appointing a DPO may be required, considering the nature, scale and risks of the processing. Even when not legally required, designating one is good accountability practice.

## Minimum compliance checklist (verify these exist for the system)

1. Data processing policy. 2. Identified controller + contact channel. 3. Information on what is processed and why. 4. Security measures. 5. Procedure to exercise ARSOP rights. 6. Right to complain to the Agency. 7. International transfers handling. 8. Retention periods. 9. Data origin. 10. Right to withdraw consent. 11. Disclosure of automated decisions.

## Project-specific notes

(Each project completes this: exact retention periods per field, the public transparency disclosure (art. 14 ter), consent requirements, whether a DPO is designated, and any additional obligations.)
