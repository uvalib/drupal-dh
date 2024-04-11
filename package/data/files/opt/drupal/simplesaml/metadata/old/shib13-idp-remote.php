<?php
/**
 * SAML 1.1 remote IdP metadata for simpleSAMLphp.
 *
 * Remember to remove the IdPs you don't use from this file.
 *
 * See: https://simplesamlphp.org/docs/stable/simplesamlphp-reference-idp-remote
 */

/*
$metadata['theproviderid-of-the-idp'] = array(
	'SingleSignOnService'  => 'https://idp.example.org/shibboleth-idp/SSO',
	'certFingerprint'      => 'c7279a9f28f11380509e072441e3dc55fb9ab864',
);
*/
$metadata['urn:mace:incommon:virginia.edu'] = array (
  'entityid' => 'urn:mace:incommon:virginia.edu',
  'contacts' => 
  array (
  ),
  'metadata-set' => 'shib13-idp-remote',
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
  'metadata-set' => 'shib13-idp-remote',
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
