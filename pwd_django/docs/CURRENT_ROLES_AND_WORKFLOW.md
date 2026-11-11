# Current Roles and Assessment Workflow

This document records the behavior implemented by the application before the
planned workflow and authorization improvements. It describes the current
baseline; it is not a statement that every permission is already sufficiently
enforced.

## User roles

| Role value | Display name | Current dashboard | Intended scope |
|---|---|---|---|
| `pwd` | PWD Citizen | `/pwd/dashboard/` | Own profile, applications, assessments, and certificates |
| `medical_officer` | Medical Officer | `/medical/dashboard/` | Eligible pending assessments at the officer's hospital and reviews completed by that officer |
| `health_officer` | Health Officer | `/health/dashboard/` | Pending health reviews at the officer's hospital and reviews completed by that officer |
| `county_officer` | County Officer | `/supervisor/dashboard/` | Hospitals, officers, assessments, and certification within one county |
| `hospital_admin` | Hospital Admin | `/hospital/dashboard/` | Officers and assessment summaries for one hospital |
| `national_admin` | National Admin | `/national/dashboard/` | National reporting and county-officer administration |

PWD citizens authenticate with an identity number. Approved officials
authenticate with a licence identifier. Newly registered officials remain
unapproved until activated through an administrative workflow.

## Current assessment states

The `Assessment` model currently stores the following states:

| Stored value | Display label | Meaning in the current implementation |
|---|---|---|
| `pending` | Pending | Application is waiting for medical review |
| `checked` | Medical Review Complete | A medical officer has completed the clinical review |
| `approved_by_health_officer` | Health Officer Approved | The hospital-level health review is complete |
| `approved_by_county_officer` | Fully Certified | The county-level decision is complete and the certificate is available |
| `rejected` | Rejected | The application was rejected during review |

## Current successful path

```text
PWD citizen submits application
  -> pending
  -> medical officer completes assessment
  -> checked
  -> health officer approves
  -> approved_by_health_officer
  -> county officer approves
  -> approved_by_county_officer
  -> certificate can be printed and publicly verified
```

## Current ownership relationships

- An assessment belongs to one PWD citizen.
- An assessment may be associated with one hospital.
- An assessment may reference the medical, health, and county officers involved.
- A hospital belongs to one county.
- Officials may be associated with a county, hospital, and designation depending on role.
- A designation may limit a medical officer to specified disability types.

## Known gaps reserved for later phases

- Correction, resubmission, revocation, and reissue paths still need approved policy definitions.
- Important transitions do not yet have a complete append-only audit trail.

Status transitions and negative permission boundaries were implemented in
Phases 2 and 4. The remaining gaps are addressed in Phases 5 and 6 of
[`IMPROVEMENT_PLAN.md`](IMPROVEMENT_PLAN.md).
