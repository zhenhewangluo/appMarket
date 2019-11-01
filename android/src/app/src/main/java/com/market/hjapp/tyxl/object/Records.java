package com.market.hjapp.tyxl.object;

import android.app.Activity;
import android.content.SharedPreferences;
import android.util.Log;

/**
 * 数据存储
 * @author Administrator
 *
 */
public class Records {

	static String recordName = "AdPlatform";

	public static int getInt0(Activity Matext, String strName) {
		SharedPreferences user = Matext.getSharedPreferences(recordName,
				Activity.MODE_PRIVATE);
		return user.getInt(strName, -1);
	}

	public static int getInt1(Activity Matext, String strName) {
		SharedPreferences user = Matext.getSharedPreferences(recordName,
				Activity.MODE_PRIVATE);
		return user.getInt(strName + 0, -1);
	}

	public static int loadIntRecord(Activity Matext, String strName) {
		int result = -1;
		try {
			SharedPreferences user = Matext.getSharedPreferences(recordName,
					Activity.MODE_PRIVATE);
			result = user.getInt(strName, -1);
		} catch (Exception e) {
			Log.v("Error-0", "" + e);
		}
		return result;
	}

	public static void saveIntRecord(Activity Matext, String strName,
			int strContent) {
		try {
			SharedPreferences user = Matext.getSharedPreferences(recordName,
					Activity.MODE_PRIVATE);
			SharedPreferences.Editor editor = user.edit();
			editor.putInt(strName, strContent);
			editor.commit();
		} catch (Exception e) {
			Log.v("Error-1", "" + e);
		}
	}

	public static int[] loadIntRecord(Activity Matext, String strName,
			int[] strContent) {
		int[] result = new int[strContent.length];
		try {
			SharedPreferences user = Matext.getSharedPreferences(recordName,
					Activity.MODE_PRIVATE);
			for (int i = 0; i < strContent.length; i++) {
				result[i] = user.getInt(strName + i, 0);
			}
		} catch (Exception e) {
			Log.v("Error-2", "" + e);
		}
		return result;
	}

	public void saveIntRecord(Activity Matext, String strName, int[] strContent) {
		try {
			SharedPreferences user = Matext.getSharedPreferences(recordName,
					Activity.MODE_PRIVATE);
			SharedPreferences.Editor editor = user.edit();
			for (int i = 0; i < strContent.length; i++) {
				editor.putInt(strName + i, strContent[i]);
			}
			editor.commit();
		} catch (Exception e) {
			Log.v("Error-3", "" + e);
		}
	}

	public static void deletIntRecord(Activity Matext, String strName,
			int[] strContent) {
		try {
			SharedPreferences user = Matext.getSharedPreferences(recordName,
					Activity.MODE_PRIVATE);
			SharedPreferences.Editor editor = user.edit();
			editor.clear();
			editor.commit();
		} catch (Exception e) {
			Log.v("Error-4", "" + e);
		}
	}

	// String 数据存储
	public static String getString0(Activity Matext, String strName) {
		SharedPreferences user = Matext.getSharedPreferences(recordName,
				Activity.MODE_PRIVATE);
		return user.getString(strName, "");
	}

	public static String getString1(Activity Matext, String strName) {
		SharedPreferences user = Matext.getSharedPreferences(recordName,
				Activity.MODE_PRIVATE);
		return user.getString(strName + 0, "");
	}

	public static String[] loadStringRecord(Activity Matext, String strName,
			String[] strContent) {
		String[] result = new String[strContent.length];
		try {
			SharedPreferences user = Matext.getSharedPreferences(recordName,
					Activity.MODE_PRIVATE);
			for (int i = 0; i < strContent.length; i++) {
				result[i] = user.getString(strName + i, "");
			}
		} catch (Exception e) {
			Log.v("Error-5", "" + e);
		}
		return result;
	}

	public static void saveStringRecord(Activity Matext, String strName,
			String[] strContent) {
		try {
			SharedPreferences user = Matext.getSharedPreferences(recordName,
					Activity.MODE_PRIVATE);
			SharedPreferences.Editor editor = user.edit();
			for (int i = 0; i < strContent.length; i++) {
				editor.putString(strName + i, strContent[i]);
			}
			editor.commit();
		} catch (Exception e) {
			Log.v("Error-6", "" + e);
		}
	}

	public static String loadStringRecord(Activity Matext, String strName) {
		String result = null;
		try {
			SharedPreferences user = Matext.getSharedPreferences(recordName,
					Activity.MODE_PRIVATE);

			result = user.getString(strName, "");
		} catch (Exception e) {
			Log.v("Error-7", "" + e);
		}
		return result;
	}

	public static void saveStringRecord(Activity Matext, String strName,
			String strContent) {
		try {
			SharedPreferences user = Matext.getSharedPreferences(recordName,
					Activity.MODE_PRIVATE);
			SharedPreferences.Editor editor = user.edit();
			editor.putString(strName, strContent);
			editor.commit();
		} catch (Exception e) {
			Log.v("Error-8", "" + e);
		}
	}

	public void deletStringRecord(Activity Matext, String strName,
			String[] strContent) {
		try {
			SharedPreferences user = Matext.getSharedPreferences(recordName,
					Activity.MODE_PRIVATE);
			SharedPreferences.Editor editor = user.edit();
			editor.clear();
			editor.commit();
		} catch (Exception e) {
			Log.v("Error-9", "" + e);
		}
	}
}
