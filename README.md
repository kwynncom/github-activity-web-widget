# github-activity-web-widget
GitHub activity monitor/update for web display


NOTES

The HTTP response header for next pages:

link: <https://api.github.com/user/14192685/repos?page=2>; rel="next", <https://api.github.com/user/14192685/repos?page=2>; rel="last"


This repository is my 31st, and that is the exact number at which the GitHub APT GET rolls over.  That is, I have to make at least 2 calls to get 31+ repos.
I guess in context that's really lucky because I encountered the problem while I was coding this.


REFERENCE

https://developer.github.com/v3/repos/#list-public-repositories


MORE GENRERAL

I have found myself looking for this command over and over.  I search for it under "dratted" in my files.  Now "dratted" is finally 
on the same line:

dratted: git remote set-url origin git@github.com:kwynncom/github-activity-web-widget.git
