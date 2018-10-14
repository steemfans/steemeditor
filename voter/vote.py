#!/usr/bin/python3
#encoding:UTF-8
import json, os, sys, time, signal,datetime
from contextlib import suppress
from steem import Steem
import pymysql

now_time = datetime.datetime.now()
start_time = (now_time - datetime.timedelta(hours=24)).strftime('%Y-%m-%d 0:00:00')
end_time = now_time.strftime('%Y-%m-%d 0:00:00')

env_dist = os.environ
db_host = env_dist.get('DATA_HOST')
db_port = int(env_dist.get('DATA_PORT'))
db_user = env_dist.get('DATA_USER')
db_pass = env_dist.get('DATA_PASS')
user_private_key = env_dist.get('STEEM_POST_KEY')
account_name = 'steemeditor.bot'

db_name = 'steemeditor'

nodes = ['https://api.steemit.com']
s = Steem(nodes, keys=[user_private_key])

def main():
    posts = get_posts()
    num = len(posts)
    if num > 0:
        val = int(100000 / num)
        if val > 10000:
            val = 10000
        weight = val / 100
        print('vote weight', weight)
        for p in posts:
            try:
                r = s.commit.vote(p[1]+'/'+p[2], weight, account=account_name)
                with pymysql.connect(
                        host=db_host,
                        port=db_port,
                        user=db_user,
                        password=db_pass,
                        charset='utf8mb4',
                        db=db_name,
                        autocommit=True) as db:
                    sql = '''update post_queue
                            set status = 1
                            where id = %s'''
                    db.execute(sql, (p[0],))
                    print('vote '+p[1]+'/'+p[2]+' success!')
            except Exception as e:
                print('vote '+p[1]+'/'+p[2]+' failed!', e)
    else:
        print('no posts')

def get_posts():
    with pymysql.connect(
            host=db_host,
            port=db_port,
            user=db_user,
            password=db_pass,
            charset='utf8mb4',
            db=db_name,
            autocommit=True) as db:
        sql = '''
            select * from post_queue
            where created_at >= "%s"
            and created_at < "%s"
            and status = 0
            order by id asc''' % (start_time, end_time)
        db.execute(sql)
        posts = db.fetchall()
        return posts

if __name__ == '__main__':
    with suppress(KeyboardInterrupt):
        main()
