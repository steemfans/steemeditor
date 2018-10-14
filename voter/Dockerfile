FROM alpine:3.7
WORKDIR /app
RUN apk --no-cache add python3 python3-dev gcc git musl-dev libffi-dev openssl-dev \
        && pip3 install steem \
        && pip3 install pymysql
ENV DATA_HOST=localhost \
    DATA_USER=root \
    DATA_PASS=123456 \
    DATA_PORT=3306 \
    STEEM_POST_KEY=abc
COPY . /app
CMD ["/app/vote.py"]
