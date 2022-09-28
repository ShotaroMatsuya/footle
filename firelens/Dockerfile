FROM amazon/aws-for-fluent-bit:latest

COPY fluentbit/fluent-bit_custom.conf /fluent-bit/etc/fluent-bit_custom.conf
COPY fluentbit/inputs.conf /fluent-bit/etc/inputs.conf
COPY fluentbit/stream_processing.conf /fluent-bit/etc/stream_processing.conf
COPY fluentbit/parsers_custom.conf /fluent-bit/etc/parsers_custom.conf
COPY fluentbit/mod_record.lua /fluent-bit/etc/mod_record.lua
COPY fluentbit/add_tag.lua /fluent-bit/etc/add_tag.lua 