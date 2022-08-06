vcl 4.0;

include "sulu.vcl";

acl invalidators {
    "localhost";
}

backend default{
    .host = "apache";
    .port = "8000";
}

sub vcl_recv {
    call sulu_recv;

    # Force the lookup, the backend must tell not to cache or vary on all
    # headers that are used to build the hash.
    return (hash);
}

sub vcl_backend_response {
    call sulu_backend_response;
}

sub vcl_deliver {
    call sulu_deliver;
}