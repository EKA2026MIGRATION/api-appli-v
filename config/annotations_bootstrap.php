<?php

// Legacy @SWG (Swagger 2.0) annotations are unresolvable since the upgrade to
// zircote/swagger-php 5 (the Swagger\Annotations namespace was removed). Ignore
// them so the route annotation reader does not fail. The OpenAPI doc built from
// these must be migrated to @OA annotations separately.
use Doctrine\Common\Annotations\AnnotationReader;

if (class_exists(AnnotationReader::class)) {
    AnnotationReader::addGlobalIgnoredNamespace('Swagger');
}
