#
# Automatic generated configuration for netflow.
# Do not edit this file manually.
#
{%
  if helpers.exists('Reticen8.Netflow.collect.enable')
  and
  Reticen8.Netflow.collect.enable|default('0') == "1"
%}
flowd_enable="YES"
{% else %}
flowd_enable="NO"
{% endif %}
