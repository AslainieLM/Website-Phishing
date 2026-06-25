# -*- coding: utf-8 -*-
"""CLI entry point for the Random Forest phishing classifier."""

import os
import sys

SCRIPT_DIR = os.path.dirname(os.path.abspath(__file__))
os.chdir(SCRIPT_DIR)

try:
	import joblib
except ImportError:
	from sklearn.externals import joblib

import inputScript

MODEL_PATH = os.path.join(SCRIPT_DIR, 'rf_final.pkl')
SAFE_OUTPUT = ' THIS IS NOT PHISHING URL'
PHISHING_OUTPUT = ' THIS IS PHISHING URL'


def main():
	if len(sys.argv) < 2:
		print('Usage: python index.py <url>', file=sys.stderr)
		sys.exit(2)

	if not os.path.isfile(MODEL_PATH):
		print('Model file rf_final.pkl not found in project root.', file=sys.stderr)
		sys.exit(3)

	url = sys.argv[1]
	classifier = joblib.load(MODEL_PATH)
	features = inputScript.main(url)
	prediction = classifier.predict(features)

	if prediction == 1:
		print(PHISHING_OUTPUT)
	else:
		print(SAFE_OUTPUT)


if __name__ == '__main__':
	main()
