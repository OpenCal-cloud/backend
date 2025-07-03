# OpenCal - Open Source Appointment Scheduling Platform

[![pipeline status](https://git.var-lab.com/opencal/backend/badges/main/pipeline.svg)](https://git.var-lab.com/opencal/backend/-/commits/main)
[![coverage report](https://git.var-lab.com/opencal/backend/badges/main/coverage.svg)](https://git.var-lab.com/opencal/backend/-/commits/main)
[![Latest Release](https://git.var-lab.com/opencal/backend/-/badges/release.svg)](https://git.var-lab.com/opencal/backend/-/releases)

This is the **OpenCal** backend API. OpenCal is a open-source web application that simplifies and streamlines the
process of scheduling
appointments.
It’s ideal for anyone who organizes meetings and wants to save time, whether they are self-employed, part of a
team or employed by a company.
Due to its open license, OpenCal can be used, customized, and self-hosted free of charge. It is also suitable for
professional and commercial use in businesses.

The API is created with Symfony and API Platform, and it provides a RESTful API that can be integrated any application
you want.

## Documentation

- [Configuration](docs/config.md)
- [Getting started (for developers)](docs/dev_setup.md)
- [How to contribute](CONTRIBUTING.md)
- [Admin documentation](docs/admin/index.md)

### Run [hadolint](https://github.com/hadolint/hadolint)

```bash
docker run --rm -i hadolint/hadolint < Dockerfile
# OR
docker run --rm -i ghcr.io/hadolint/hadolint < Dockerfile
```

OpenCal is licensed under the [GNU AGPLv3 License](LICENSE).

Created by [var-lab IT GmbH](https://var-lab.com) (from Nuremberg, Germany) and contributors.
