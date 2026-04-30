import shutil
import os

src = r"C:\Users\hp``\.gemini\antigravity\brain\f27b5b53-3338-4a06-8264-26c054526a6d\media__1776547460150.png"
dst = r"c:\wamp64\www\web_project_lab\online_exam_system\assets\images\hero-illustration.png"

try:
    if os.path.exists(src):
        shutil.copy(src, dst)
        print(f"Successfully copied from {src} to {dst}")
    else:
        # Try finding it with listdir to check the actual name
        parent = r"C:\Users\hp``\.gemini\antigravity\brain\f27b5b53-3338-4a06-8264-26c054526a6d"
        if os.path.exists(parent):
            print(f"Parent directory exists: {parent}")
            print(f"Contents: {os.listdir(parent)}")
        else:
            print(f"Parent directory NOT found: {parent}")
except Exception as e:
    print(f"Error: {e}")
