import shutil
import os

src = r"C:\Users\hp``\.gemini\antigravity\brain\f27b5b53-3338-4a06-8264-26c054526a6d\hero_illustration_1776547611527.png"
dst = r"c:\wamp64\www\web_project_lab\online_exam_system\assets\images\hero_premium.png"

try:
    if os.path.exists(src):
        shutil.copy(src, dst)
        print(f"Successfully copied from {src} to {dst}")
except Exception as e:
    print(f"Error: {e}")
