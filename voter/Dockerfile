FROM alpine:3.7
WORKDIR /app
RUN apk --no-cache add python3 python3-dev gcc git musl-dev libffi-dev openssl-dev \
        && pip3 install steem \
        && pip3 install pymysql
ENV DB_HOST=localhost \
    DB_USER=root \
    DB_PASS=123456 \
    DB_PORT=3306 \
    DB_NAME=steem-editor \
    STEEM_POST_KEY=abc
COPY . /app
CMD ["/app/vote.py"]
