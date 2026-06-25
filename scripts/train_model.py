# -*- coding: utf-8 -*-
"""Retrain rf_final.pkl for modern scikit-learn (run from project root)."""

import os
import sys

import joblib
import pandas as pd
from sklearn.ensemble import RandomForestClassifier
from sklearn.model_selection import train_test_split

ROOT = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
DATASET = os.path.join(ROOT, 'datasets', 'phishcoop.csv')
MODEL_OUT = os.path.join(ROOT, 'rf_final.pkl')


def main():
	if not os.path.isfile(DATASET):
		print('Dataset not found:', DATASET, file=sys.stderr)
		sys.exit(1)

	dataset = pd.read_csv(DATASET)
	if 'id' in dataset.columns:
		dataset = dataset.drop('id', axis=1)

	x = dataset.iloc[:, :-1].values
	y = dataset.iloc[:, -1].values.ravel()

	x_train, x_test, y_train, y_test = train_test_split(
		x, y, test_size=0.25, random_state=0
	)

	classifier = RandomForestClassifier(
		n_estimators=100,
		criterion='gini',
		max_features='log2',
		random_state=0,
		n_jobs=-1,
	)
	classifier.fit(x_train, y_train)
	accuracy = classifier.score(x_test, y_test)

	joblib.dump(classifier, MODEL_OUT)
	print('Model saved to', MODEL_OUT)
	print('Test accuracy:', round(accuracy * 100, 2), '%')


if __name__ == '__main__':
	main()
