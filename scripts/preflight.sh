#!/usr/bin/env bash
set -u

urls=(
  "https://guidevitalite.site/"
  "https://www.guidevitalite.site/"
  "http://guidevitalite.site/"
  "http://www.guidevitalite.site/"
  "https://guidevitalite.site/forcevital-avis/"
  "https://guidevitalite.site/repellio/"
  "https://guidevitalite.site/slimqa/"
  "https://guidevitalite.site/a-propos/"
  "https://guidevitalite.site/contact/"
  "https://guidevitalite.site/confidentialite/"
  "https://guidevitalite.site/conditions/"
  "https://guidevitalite.site/divulgation-affiliation/"
  "https://guidevitalite.site/robots.txt"
  "https://guidevitalite.site/sitemap.xml"
)

for url in "${urls[@]}"; do
  curl -sS -o /dev/null -L --max-redirs 10 -w 'URL testée : %{url}\nStatut HTTP : %{http_code}\nDestination : %{redirect_url}\nURL finale : %{url_effective}\nNombre de redirections : %{num_redirects}\n\n' "$url"
done

redirects=(
  "forcevital"
  "repellio"
  "slimqa"
)

for redirect in "${redirects[@]}"; do
  result="$(curl -sS -o /dev/null --max-redirs 0 -w '%{http_code}|%{redirect_url}' "https://guidevitalite.site/go/${redirect}/?gclid=preflight-test")"
  IFS='|' read -r status destination <<< "$result"

  if [[ "$status" == "302" && "$destination" == *"gclid=preflight-test"* ]]; then
    echo "Redirection ${redirect} : OK (302 avec gclid conservé)"
  else
    echo "Redirection ${redirect} : ERREUR (statut ${status}, destination ${destination})"
  fi
done

tmp_dir="$(mktemp -d)"
trap 'rm -r "$tmp_dir"' EXIT
landings=(
  "forcevital-avis"
  "repellio"
  "slimqa"
)

for landing in "${landings[@]}"; do
  page="https://guidevitalite.site/${landing}/"
  curl -sS "$page" -o "$tmp_dir/${landing}-navigateur.html"
  curl -sS -A 'Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)' "$page" -o "$tmp_dir/${landing}-googlebot.html"
  curl -sS -A 'AdsBot-Google (+http://www.google.com/adsbot.html)' "$page" -o "$tmp_dir/${landing}-adsbot.html"
  echo "Empreintes du contenu essentiel de ${landing} :"
  sha256sum "$tmp_dir/${landing}-"*.html
  cmp -s "$tmp_dir/${landing}-navigateur.html" "$tmp_dir/${landing}-googlebot.html" && echo "Navigateur et Googlebot : identiques" || echo "Navigateur et Googlebot : différents"
  cmp -s "$tmp_dir/${landing}-navigateur.html" "$tmp_dir/${landing}-adsbot.html" && echo "Navigateur et AdsBot-Google : identiques" || echo "Navigateur et AdsBot-Google : différents"
done
