<?php
/**
 * SAML 2.0 remote IdP metadata for simpleSAMLphp.
 *
 * Remember to remove the IdPs you don't use from this file.
 *
 * See: https://simplesamlphp.org/docs/stable/simplesamlphp-reference-idp-remote 
 */

/*
 * Guest IdP. allows users to sign up and register. Great for testing!
 */
$metadata['https://sts.windows.net/5b9b0da3-7d05-4e30-b44d-a0a98c8ed67e/'] = array (
  'entityid' => 'https://sts.windows.net/5b9b0da3-7d05-4e30-b44d-a0a98c8ed67e/',
  'contacts' => 
  array (
  ),
  'metadata-set' => 'saml20-idp-remote',
  'SingleSignOnService' => 
  array (
    0 => 
    array (
      'Binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect',
      'Location' => 'https://login.windows.net/5b9b0da3-7d05-4e30-b44d-a0a98c8ed67e/saml2',
    ),
    1 => 
    array (
      'Binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST',
      'Location' => 'https://login.windows.net/5b9b0da3-7d05-4e30-b44d-a0a98c8ed67e/saml2',
    ),
  ),
  'SingleLogoutService' => 
  array (
    0 => 
    array (
      'Binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect',
      'Location' => 'https://login.windows.net/5b9b0da3-7d05-4e30-b44d-a0a98c8ed67e/saml2',
    ),
  ),
  'ArtifactResolutionService' => 
  array (
  ),
  'keys' => 
  array (
    0 => 
    array (
      'encryption' => false,
      'signing' => true,
      'type' => 'X509Certificate',
      'X509Certificate' => 'MIIDPjCCAiqgAwIBAgIQsRiM0jheFZhKk49YD0SK1TAJBgUrDgMCHQUAMC0xKzApBgNVBAMTImFjY291bnRzLmFjY2Vzc2NvbnRyb2wud2luZG93cy5uZXQwHhcNMTQwMTAxMDcwMDAwWhcNMTYwMTAxMDcwMDAwWjAtMSswKQYDVQQDEyJhY2NvdW50cy5hY2Nlc3Njb250cm9sLndpbmRvd3MubmV0MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAkSCWg6q9iYxvJE2NIhSyOiKvqoWCO2GFipgH0sTSAs5FalHQosk9ZNTztX0ywS/AHsBeQPqYygfYVJL6/EgzVuwRk5txr9e3n1uml94fLyq/AXbwo9yAduf4dCHTP8CWR1dnDR+Qnz/4PYlWVEuuHHONOw/blbfdMjhY+C/BYM2E3pRxbohBb3x//CfueV7ddz2LYiH3wjz0QS/7kjPiNCsXcNyKQEOTkbHFi3mu0u13SQwNddhcynd/GTgWN8A+6SN1r4hzpjFKFLbZnBt77ACSiYx+IHK4Mp+NaVEi5wQtSsjQtI++XsokxRDqYLwus1I1SihgbV/STTg5enufuwIDAQABo2IwYDBeBgNVHQEEVzBVgBDLebM6bK3BjWGqIBrBNFeNoS8wLTErMCkGA1UEAxMiYWNjb3VudHMuYWNjZXNzY29udHJvbC53aW5kb3dzLm5ldIIQsRiM0jheFZhKk49YD0SK1TAJBgUrDgMCHQUAA4IBAQCJ4JApryF77EKC4zF5bUaBLQHQ1PNtA1uMDbdNVGKCmSf8M65b8h0NwlIjGGGy/unK8P6jWFdm5IlZ0YPTOgzcRZguXDPj7ajyvlVEQ2K2ICvTYiRQqrOhEhZMSSZsTKXFVwNfW6ADDkN3bvVOVbtpty+nBY5UqnI7xbcoHLZ4wYD251uj5+lo13YLnsVrmQ16NCBYq2nQFNPuNJw6t3XUbwBHXpF46aLT1/eGf/7Xx6iy8yPJX4DyrpFTutDz882RWofGEO5t4Cw+zZg70dJ/hH/ODYRMorfXEW+8uKmXMKmX2wyxMKvfiPbTy5LmAU8Jvjs2tLg4rOBcXWLAIarZ',
    ),
    1 => 
    array (
      'encryption' => false,
      'signing' => true,
      'type' => 'X509Certificate',
      'X509Certificate' => 'MIIC4jCCAcqgAwIBAgIQQNXrmzhLN4VGlUXDYCRT3zANBgkqhkiG9w0BAQsFADAtMSswKQYDVQQDEyJhY2NvdW50cy5hY2Nlc3Njb250cm9sLndpbmRvd3MubmV0MB4XDTE0MTAyODAwMDAwMFoXDTE2MTAyNzAwMDAwMFowLTErMCkGA1UEAxMiYWNjb3VudHMuYWNjZXNzY29udHJvbC53aW5kb3dzLm5ldDCCASIwDQYJKoZIhvcNAQEBBQADggEPADCCAQoCggEBALyKs/uPhEf7zVizjfcr/ISGFe9+yUOqwpel38zgutvLHmFD39E2hpPdQhcXn4c4dt1fU5KvkbcDdVbP8+e4TvNpJMy/nEB2V92zCQ/hhBjilwhF1ETe1TMmVjALs0KFvbxW9ZN3EdUVvxFvz/gvG29nQhl4QWKj3x8opr89lmq14Z7T0mzOV8kub+cgsOU/1bsKqrIqN1fMKKFhjKaetctdjYTfGzVQ0AJAzzbtg0/Q1wdYNAnhSDafygEv6kNiquk0r0RyasUUevEXs2LY3vSgKsKseI8ZZlQEMtE9/k/iAG7JNcEbVg53YTurNTrPnXJOU88mf3TToX14HpYsS1ECAwEAATANBgkqhkiG9w0BAQsFAAOCAQEAfolx45w0i8CdAUjjeAaYdhG9+NDHxop0UvNOqlGqYJexqPLuvX8iyUaYxNGzZxFgGI3GpKfmQP2JQWQ1E5JtY/n8iNLOKRMwqkuxSCKJxZJq4Sl/m/Yv7TS1P5LNgAj8QLCypxsWrTAmq2HSpkeSk4JBtsYxX6uhbGM/K1sEktKybVTHu22/7TmRqWTmOUy9wQvMjJb2IXdMGLG3hVntN/WWcs5w8vbt1i8Kk6o19W2MjZ95JaECKjBDYRlhG1KmSBtrsKsCBQoBzwH/rXfksTO9JoUYLXiW0IppB7DhNH4PJ5hZI91R8rR0H3/bKkLSuDaKLWSqMhozdhXsIIKvJQ==',
    ),
  ),
);

$metadata['urn:mace:incommon:virginia.edu'] = array (
  'entityid' => 'urn:mace:incommon:virginia.edu',
  'contacts' => 
  array (
  ),
  'metadata-set' => 'saml20-idp-remote',
  'SingleSignOnService' => 
  array (
    0 => 
    array (
      'Binding' => 'urn:mace:shibboleth:1.0:profiles:AuthnRequest',
      'Location' => 'https://shibidp.its.virginia.edu/idp/profile/Shibboleth/SSO',
    ),
    1 => 
    array (
      'Binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST',
      'Location' => 'https://shibidp.its.virginia.edu/idp/profile/SAML2/POST/SSO',
    ),
    2 => 
    array (
      'Binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST-SimpleSign',
      'Location' => 'https://shibidp.its.virginia.edu/idp/profile/SAML2/POST-SimpleSign/SSO',
    ),
    3 => 
    array (
      'Binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect',
      'Location' => 'https://shibidp.its.virginia.edu/idp/profile/SAML2/Redirect/SSO',
    ),
  ),
  'SingleLogoutService' => 
  array (
  ),
  'ArtifactResolutionService' => 
  array (
    0 => 
    array (
      'Binding' => 'urn:oasis:names:tc:SAML:1.0:bindings:SOAP-binding',
      'Location' => 'https://shibidp.its.virginia.edu:8443/idp/profile/SAML1/SOAP/ArtifactResolution',
      'index' => 1,
    ),
    1 => 
    array (
      'Binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:SOAP',
      'Location' => 'https://shibidp.its.virginia.edu:8443/idp/profile/SAML2/SOAP/ArtifactResolution',
      'index' => 2,
    ),
  ),
  'keys' => 
  array (
    0 => 
    array (
      'encryption' => true,
      'signing' => true,
      'type' => 'X509Certificate',
      'X509Certificate' => '
MIIDOzCCAiOgAwIBAgIVALIs8V8u06NEcoiPKqBdTWQ5F3WdMA0GCSqGSIb3DQEB
BQUAMCMxITAfBgNVBAMTGHNoaWJpZHAuaXRzLnZpcmdpbmlhLmVkdTAeFw0xMjAz
MDIxNTI2MDNaFw0zMjAzMDIxNTI2MDNaMCMxITAfBgNVBAMTGHNoaWJpZHAuaXRz
LnZpcmdpbmlhLmVkdTCCASIwDQYJKoZIhvcNAQEBBQADggEPADCCAQoCggEBAMz9
8k0PGFjm33ceTUVhpw4fWO+oknxcOTL8o+nsnD3jiaF7KIBWs70+M9Ddkl8ih/os
rXCPzBhmB/ttNgaKGczqGKCq+o1+cjBgrHzxfUOQAr6ne6ZyZgN0VbRNvkNDB9Te
Gf9BAlByFcLbrM9xYfu5z79deO9m/M5q6FbD5QMY1qN8A5oJhi1IPZo5GuFCoUJz
mWGRXSujZQHBFr5T+euyMxnC3Gr+yJhP5plm9tET5VEf/tCWmeeWam84e1u9LP2n
bFYpusfGZ9lA/JgoYOdYTb5gcHb53yIzLxUF/KaUQACMqbIXZxpvg+7qpLZkBJIX
G/Qipg5ProkW7GqyzrcCAwEAAaNmMGQwQwYDVR0RBDwwOoIYc2hpYmlkcC5pdHMu
dmlyZ2luaWEuZWR1hh51cm46bWFjZTppbmNvbW1vbjp2aXJnaW5pYS5lZHUwHQYD
VR0OBBYEFJe4yrDCKYu70HZV9azIdbPqM9KHMA0GCSqGSIb3DQEBBQUAA4IBAQB8
G07ktM6zaMsydtat8FUHbQsDqMu51vJAg8DTHD63SoJSG/NFar8BNZH0DDb33Zyy
4KXfVGzE5Jtg5cb/5eRLah42FtkfvXnSMKgn8jAx77jG3kD/okm0iCKP6RRS7L7Q
l3CESXZORAXC50pPjoacANRdAl729CszcW65zUhoKBV37plZq5uRc7FTtjJujEOL
0wsZTq9SmdmHtH3E+XabESqWp43vuMTNS2XPBQPIQUyuCldZN+N6jLkOGQI104P3
3FljH7rKPo43+7MT2XZGS2J+PcALgUfSxewXei0RQNXWXN3l0qXupwsWM8kO6mSA
zZQUr9YEODP0DFBKKGSo

                    ',
    ),
  ),
  'scope' => 
  array (
    0 => 'virginia.edu',
  ),
);
$metadata['https://shibidp-test.its.virginia.edu/idp/shibboleth'] = array (
  'entityid' => 'https://shibidp-test.its.virginia.edu/idp/shibboleth',
  'contacts' => 
  array (
  ),
  'metadata-set' => 'saml20-idp-remote',
  'SingleSignOnService' => 
  array (
    0 => 
    array (
      'Binding' => 'urn:mace:shibboleth:1.0:profiles:AuthnRequest',
      'Location' => 'https://shibidp-test.its.virginia.edu/idp/profile/Shibboleth/SSO',
    ),
    1 => 
    array (
      'Binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST',
      'Location' => 'https://shibidp-test.its.virginia.edu/idp/profile/SAML2/POST/SSO',
    ),
    2 => 
    array (
      'Binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST-SimpleSign',
      'Location' => 'https://shibidp-test.its.virginia.edu/idp/profile/SAML2/POST-SimpleSign/SSO',
    ),
    3 => 
    array (
      'Binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect',
      'Location' => 'https://shibidp-test.its.virginia.edu/idp/profile/SAML2/Redirect/SSO',
    ),
    4 => 
    array (
      'Binding' => 'urn:liberty:sb:2006-08',
      'Location' => 'https://shibidp-test.its.virginia.edu:8443/idp/profile/IDWSF/SSOS',
    ),
  ),
  'SingleLogoutService' => 
  array (
  ),
  'ArtifactResolutionService' => 
  array (
    0 => 
    array (
      'Binding' => 'urn:oasis:names:tc:SAML:1.0:bindings:SOAP-binding',
      'Location' => 'https://shibidp-test.its.virginia.edu:8443/idp/profile/SAML1/SOAP/ArtifactResolution',
      'index' => 1,
    ),
    1 => 
    array (
      'Binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:SOAP',
      'Location' => 'https://shibidp-test.its.virginia.edu:8443/idp/profile/SAML2/SOAP/ArtifactResolution',
      'index' => 2,
    ),
  ),
  'keys' => 
  array (
    0 => 
    array (
      'encryption' => true,
      'signing' => true,
      'type' => 'X509Certificate',
      'X509Certificate' => '
MIIDOzCCAiOgAwIBAgIVALIs8V8u06NEcoiPKqBdTWQ5F3WdMA0GCSqGSIb3DQEB
BQUAMCMxITAfBgNVBAMTGHNoaWJpZHAuaXRzLnZpcmdpbmlhLmVkdTAeFw0xMjAz
MDIxNTI2MDNaFw0zMjAzMDIxNTI2MDNaMCMxITAfBgNVBAMTGHNoaWJpZHAuaXRz
LnZpcmdpbmlhLmVkdTCCASIwDQYJKoZIhvcNAQEBBQADggEPADCCAQoCggEBAMz9
8k0PGFjm33ceTUVhpw4fWO+oknxcOTL8o+nsnD3jiaF7KIBWs70+M9Ddkl8ih/os
rXCPzBhmB/ttNgaKGczqGKCq+o1+cjBgrHzxfUOQAr6ne6ZyZgN0VbRNvkNDB9Te
Gf9BAlByFcLbrM9xYfu5z79deO9m/M5q6FbD5QMY1qN8A5oJhi1IPZo5GuFCoUJz
mWGRXSujZQHBFr5T+euyMxnC3Gr+yJhP5plm9tET5VEf/tCWmeeWam84e1u9LP2n
bFYpusfGZ9lA/JgoYOdYTb5gcHb53yIzLxUF/KaUQACMqbIXZxpvg+7qpLZkBJIX
G/Qipg5ProkW7GqyzrcCAwEAAaNmMGQwQwYDVR0RBDwwOoIYc2hpYmlkcC5pdHMu
dmlyZ2luaWEuZWR1hh51cm46bWFjZTppbmNvbW1vbjp2aXJnaW5pYS5lZHUwHQYD
VR0OBBYEFJe4yrDCKYu70HZV9azIdbPqM9KHMA0GCSqGSIb3DQEBBQUAA4IBAQB8
G07ktM6zaMsydtat8FUHbQsDqMu51vJAg8DTHD63SoJSG/NFar8BNZH0DDb33Zyy
4KXfVGzE5Jtg5cb/5eRLah42FtkfvXnSMKgn8jAx77jG3kD/okm0iCKP6RRS7L7Q
l3CESXZORAXC50pPjoacANRdAl729CszcW65zUhoKBV37plZq5uRc7FTtjJujEOL
0wsZTq9SmdmHtH3E+XabESqWp43vuMTNS2XPBQPIQUyuCldZN+N6jLkOGQI104P3
3FljH7rKPo43+7MT2XZGS2J+PcALgUfSxewXei0RQNXWXN3l0qXupwsWM8kO6mSA
zZQUr9YEODP0DFBKKGSo

                    ',
    ),
  ),
  'scope' => 
  array (
    0 => 'virginia.edu',
  ),
);
