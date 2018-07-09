FROM codecasts/alpine-3.7:php-7.2

WORKDIR /data

COPY . ./

CMD ["/bin/ash", "bin/docker-run.ash"]
