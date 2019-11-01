package com.market.hjapp.database;

import java.io.File;
import java.io.FileNotFoundException;
import java.io.FileOutputStream;
import java.io.IOException;
import java.util.ArrayList;
import java.util.HashMap;

import android.content.ContentValues;
import android.content.Context;
import android.database.Cursor;
import android.database.SQLException;
import android.database.sqlite.SQLiteDatabase;
import android.graphics.Bitmap;
import android.graphics.BitmapFactory;
import android.util.Log;

import com.market.hjapp.App;
import com.market.hjapp.Category;
import com.market.hjapp.GeneralUtil;
import com.market.hjapp.MyLog;
import com.market.hjapp.Recommend;

//SVN测试
public class DatabaseUtils {
	private static final String TAG = "DatabaseUtils";

	public static void saveCategoryList(Context ctx,
			ArrayList<Category> cateList) {

		SQLiteDatabase db = null;
		try {
			db = new DatabaseHelper(ctx).getWritableDatabase();

			for (Category cate : cateList) {
				MyLog.d(TAG, "replacing category " + cate.getName() + ", sig: "
						+ cate.getSig() + ", order: " + cate.getCateOrder());
				ContentValues v = createCagetoryValues(cate);

				db.replaceOrThrow(DatabaseSchema.TABLE_CATEGORY.NAME, null, v);
			}

			db.close();
		} catch (Exception e) {
			if (db != null)
				db.close();

			Log.e(TAG, e.toString());
		}
	}

	// public static void saveCategoryListInRank(Context ctx,
	// ArrayList<Category> rankList) {
	//
	// SQLiteDatabase db = null;
	// try
	// {
	// db = new DatabaseHelper(ctx).getWritableDatabase();
	//
	// // clear category first
	// db.execSQL("DELETE FROM " + DatabaseSchema.TABLE_RANK.NAME);
	//
	// for (Category cate : rankList) {
	// MyLog.d(TAG, "inserting category " + cate.getName() + ", sig: " +
	// cate.getSig());
	// ContentValues v = createRankValues(cate);
	//
	// db.insertOrThrow(DatabaseSchema.TABLE_RANK.NAME, null, v);
	// }
	//
	// db.close();
	// }
	// catch(Exception e)
	// {
	// if (db != null)
	// db.close();
	//
	// Log.e(TAG, e.toString());
	// }
	// }

	public static void saveRecommendList(Context ctx,
			ArrayList<Recommend> recommendList) {

		SQLiteDatabase db = null;
		try {
			db = new DatabaseHelper(ctx).getWritableDatabase();

			for (Recommend recommend : recommendList) {

				ContentValues cv = createRecommendValues(recommend);

				MyLog.d(TAG, "getId" + recommend.getId());
				MyLog.d(TAG, "getTargetId" + recommend.getTargetId());

				try {
					db.replaceOrThrow(DatabaseSchema.TABLE_RECOMMEND.NAME,
							null, cv);
				} catch (SQLException e) {
					// TODO: handle exception
					MyLog.e(TAG,
							"saveRecommendList,replaceOrThrow error:"
									+ e.toString());
				}

				// db.replace(DatabaseSchema.TABLE_RECOMMEND.NAME,
				// null,
				// cv);

				// if (hasRecommend(db, recommend.getId()))
				// {
				// db.update(DatabaseSchema.TABLE_RECOMMEND.NAME,
				// cv,
				// DatabaseSchema.TABLE_RECOMMEND.COLUMN_RECOMMENDID + "=?",
				// new String[] {recommend.getId()});
				// }
				// else
				// {
				// db.insertOrThrow(DatabaseSchema.TABLE_RECOMMEND.NAME,
				// null,
				// cv);
				// }

			}

			db.close();
		} catch (Exception e) {
			if (db != null)
				db.close();

			Log.e(TAG, e.toString());
		}
	}

	public static void updateCategory(SQLiteDatabase db, Category c,
			String cateId) {
		try {
			StringBuilder buf = new StringBuilder();
			buf.append(DatabaseSchema.TABLE_CATEGORY.COLUMN_CATEID);
			buf.append("=" + cateId);

			ContentValues v = new ContentValues();
			v.put(DatabaseSchema.TABLE_CATEGORY.COLUMN_APPCOUNT,
					c.getAppCount());

			db.update(DatabaseSchema.TABLE_CATEGORY.NAME, v, buf.toString(),
					null);
		} catch (Exception e) {
			Log.e(TAG, e.toString());
		}
	}

	// public static void updateCategoryIfNeeded(ArrayList<Category> cateList,
	// SQLiteDatabase db) {
	// MyLog.d(TAG, "update cate if needed >>> size: " + cateList.size());
	// Cursor c = db.query(DatabaseSchema.TABLE_CATEGORY.NAME,
	// new String[]{DatabaseSchema.TABLE_CATEGORY.COLUMN_CATEID},
	// null, null, null, null, null);
	//
	// ArrayList<String> existedCateIdList = new ArrayList<String>();
	// while (c.moveToNext()) {
	// String cateid = c.getString(0);
	// MyLog.d(TAG, "adding id: " + cateid);
	// existedCateIdList.add(cateid);
	// }
	//
	// c.close();
	//
	// for (Category cate : cateList) {
	// MyLog.d(TAG, "checking " + cate.getSig());
	// if (!existedCateIdList.contains(cate.getSig())) {
	// MyLog.d(TAG, "inserting " + cate.getSig());
	// ContentValues v = createCagetoryValues(cate);
	//
	// db.insertOrThrow(DatabaseSchema.TABLE_CATEGORY.NAME, null, v);
	// } else {
	// // TODO: has previous data, compare the categories and update if
	// necessary
	// }
	// }
	// }

	// private static boolean hasCategory(SQLiteDatabase db, String cateid,
	// String sorting) {
	// StringBuilder buf = new StringBuilder();
	// buf.append("SELECT ");
	// buf.append(DatabaseSchema.TABLE_CATEGORY.COLUMN_ID);
	// buf.append(" FROM ");
	// buf.append(DatabaseSchema.TABLE_CATEGORY.NAME);
	// buf.append(" WHERE ");
	// buf.append(DatabaseSchema.TABLE_CATEGORY.COLUMN_CATEID);
	// buf.append(" = ");
	// buf.append(cateid);
	// if (sorting != null) {
	// buf.append(" AND ");
	// buf.append(DatabaseSchema.TABLE_CATEGORY.COLUMN_SORTING);
	// buf.append(" = ");
	// buf.append(sorting);
	// }
	//
	// Cursor c = query(db, buf.toString());
	// boolean hasCate = (c.getCount() != 0);
	//
	// c.close();
	//
	// return hasCate;
	// }

	private static Cursor query(SQLiteDatabase db, String sql) {
		MyLog.d(TAG, "run query >>> " + sql);
		Log.e("sql=============", "" + sql);
		return db.rawQuery(sql, null);
	}

	private static ContentValues createCagetoryValues(Category c) {
		ContentValues v = new ContentValues();
		v.put(DatabaseSchema.TABLE_CATEGORY.COLUMN_CATE_DESC,
				c.getDescription());
		v.put(DatabaseSchema.TABLE_CATEGORY.COLUMN_CATEID, c.getSig());
		v.put(DatabaseSchema.TABLE_CATEGORY.COLUMN_PARENT_ID, c.getParentId());
		v.put(DatabaseSchema.TABLE_CATEGORY.COLUMN_ICON_URL, c.getIconUrl());
		v.put(DatabaseSchema.TABLE_CATEGORY.COLUMN_NAME, c.getName());
		v.put(DatabaseSchema.TABLE_CATEGORY.COLUMN_APPCOUNT, c.getAppCount());
		v.put(DatabaseSchema.TABLE_CATEGORY.COLUMN_TYPE, c.getType());
		v.put(DatabaseSchema.TABLE_CATEGORY.COLUMN_UPDATE_INTERVAL,
				c.getUpdateInterval());
		v.put(DatabaseSchema.TABLE_CATEGORY.COLUMN_CATE_ORDER, c.getCateOrder());

		return v;
	}

	// private static ContentValues createRankValues(Category c) {
	// ContentValues v = new ContentValues();
	// v.put(DatabaseSchema.TABLE_RANK.COLUMN_RANKID, c.getSig());
	// v.put(DatabaseSchema.TABLE_RANK.COLUMN_ICON_URL, c.getIconUrl());
	// v.put(DatabaseSchema.TABLE_RANK.COLUMN_NAME, c.getName());
	// v.put(DatabaseSchema.TABLE_RANK.COLUMN_RANK_DESC, c.getDescription());
	// return v;
	// }

	private static ContentValues createRecommendValues(Recommend r) {
		ContentValues v = new ContentValues();
		v.put(DatabaseSchema.TABLE_RECOMMEND.COLUMN_RECOMMENDID, r.getId());
		v.put(DatabaseSchema.TABLE_RECOMMEND.COLUMN_NAME, r.getName());
		v.put(DatabaseSchema.TABLE_RECOMMEND.COLUMN_DATE, r.getDate());
		v.put(DatabaseSchema.TABLE_RECOMMEND.COLUMN_RECOMMEND_DESC, r.getDesc());
		v.put(DatabaseSchema.TABLE_RECOMMEND.COLUMN_TARGET_ID, r.getTargetId());
		v.put(DatabaseSchema.TABLE_RECOMMEND.COLUMN_TARGET_TYPE,
				r.getTargetType());
		v.put(DatabaseSchema.TABLE_RECOMMEND.COLUMN_ICON_URL, r.getIconUrl());
		v.put(DatabaseSchema.TABLE_RECOMMEND.COLUMN_IMAGEA_URL, r.getImageUrl());
		return v;
	}

	// public static boolean hasAppListInCategory(SQLiteDatabase db, String
	// cate_id) {
	// StringBuilder buf = new StringBuilder();
	// buf.append("SELECT ");
	// buf.append(DatabaseSchema.TABLE_CATEGORY.COLUMN_APPLIST_NEW_FREE);
	// buf.append(" FROM ");
	// buf.append(DatabaseSchema.TABLE_CATEGORY.NAME);
	// buf.append(" WHERE ");
	// buf.append(DatabaseSchema.TABLE_CATEGORY.COLUMN_CATEID);
	// buf.append(" = ");
	// buf.append(cate_id);
	//
	// Cursor c = query(db, buf.toString());
	//
	// boolean hasCachedData = false;
	// if (c.moveToFirst()) {
	// hasCachedData = !("".equals(c.getString(0)));
	// }
	//
	// c.close();
	// return hasCachedData;
	// }

	// public static void saveAppList(Context ctx, String cateid, String
	// sorting,
	// ArrayList<App> applist) {
	// SQLiteDatabase db = new DatabaseHelper(ctx).getWritableDatabase();
	//
	// saveAppList(db, cateid, sorting, applist);
	//
	// db.close();
	// }

	/**
	 * Save app list to database. If app already exists, mark it as cached. This
	 * will also update the cate-app relationships.
	 * 
	 * @param cateid
	 *            cate id of this app.
	 * @param sorting
	 *            whether has sorting
	 * @param pageno
	 *            whether has page number
	 * @param applist
	 *            app list
	 */
	// public static void saveAppList(SQLiteDatabase db, String cateid, String
	// sorting, int pageno,
	// ArrayList<App> applist) {
	// MyLog.d(TAG, "save applist > cateid: " + cateid + ", sorting: " + sorting
	// + ", pageno: " +
	// pageno + ", applist size: " + applist.size());
	// // save apps
	// for (App app : applist) {
	// insertOrUpdateOneApp(db, app);
	// }
	//
	// if (hasCategory(db, cateid, null)) {
	// if (sorting != null) {
	// if (pageno != 0) {
	// throw new RuntimeException("Try to derive a non-first page");
	// }
	// // has a non-sorted category, insert a sorted cate from it
	// deriveCategory(db, cateid, sorting, applist);
	// } else {
	// // has previous cate, update it
	// updateCategoryAppList(db, cateid, sorting, pageno, applist);
	// }
	// } else {
	// throw new RuntimeException("No previous cate found > " + cateid);
	// }
	// }

	public static void saveAppList(SQLiteDatabase db, ArrayList<App> applist) {

		MyLog.d(TAG, "save applist > " + " applist size: " + applist.size());
		// save apps
		for (App app : applist) {

			int res = checkAppStatus(db, app);
			if (res == THIS_IS_NEW_APP) {
				app.setStatus(App.INIT);

				insertOrUpdateOneApp(db, app, true);

			} else {
				if (res == KEEP_APP_STATUS) {

				} else if (res == APP_NEED_UPDATE) {
					app.setStatus(App.HAS_UPDATE);
				}

				insertOrUpdateOneApp(db, app, false);
			}
		}
	}

	public static void saveApp(SQLiteDatabase db, App app) {

		int res = checkAppStatus(db, app);
		if (res == THIS_IS_NEW_APP) {
			app.setStatus(App.INIT);

			insertOrUpdateOneApp(db, app, true);

		} else {
			if (res == KEEP_APP_STATUS) {

			} else if (res == APP_NEED_UPDATE) {
				app.setStatus(App.HAS_UPDATE);
			}

			insertOrUpdateOneApp(db, app, false);
		}
	}

	public static String saveAppStateList(Context ctx, ArrayList<App> applist) {

		SQLiteDatabase db = new DatabaseHelper(ctx).getWritableDatabase();

		MyLog.d(TAG, "save app state list > " + " appStateList size: "
				+ applist.size());

		// save apps
		String needFullInfo = "";
		for (App app : applist) {
			if (!isDetailChanged(db, app))
				updateOneAppState(db, app);
			else
				needFullInfo += app.getId() + ",";
		}

		db.close();
		return needFullInfo;
	}

	// private static void deriveCategory(SQLiteDatabase db, String cateid,
	// String sorting,
	// ArrayList<App> applist) {
	// String originalColumnList = getCategoryColumnList(null, null);
	// String updateColumnList = getCategoryColumnList(sorting, applist);
	//
	// final String sql = "INSERT INTO " + DatabaseSchema.TABLE_CATEGORY.NAME +
	// " (" + originalColumnList + ") SELECT " + updateColumnList +
	// " FROM " + DatabaseSchema.TABLE_CATEGORY.NAME +
	// " WHERE " + DatabaseSchema.TABLE_CATEGORY.COLUMN_CATEID + "=" + cateid +
	// ";";
	//
	// db.execSQL(sql);
	// }

	// private static String getCategoryColumnList(String sorting,
	// ArrayList<App> applist) {
	// StringBuilder buf = new StringBuilder();
	// buf.append((applist != null ? "'" + appListToStr(applist) + "'" :
	// DatabaseSchema.TABLE_CATEGORY.COLUMN_APPLIST_NEW_FREE) + ",");
	// buf.append(DatabaseSchema.TABLE_CATEGORY.COLUMN_APPCOUNT + ",");
	// buf.append(DatabaseSchema.TABLE_CATEGORY.COLUMN_CATEID + ",");
	// buf.append(DatabaseSchema.TABLE_CATEGORY.COLUMN_ICON_URL + ",");
	// buf.append(DatabaseSchema.TABLE_CATEGORY.COLUMN_IS_CACHED + ",");
	// buf.append(DatabaseSchema.TABLE_CATEGORY.COLUMN_NAME + ",");
	// buf.append(DatabaseSchema.TABLE_CATEGORY.COLUMN_PARENT_ID + ",");
	// buf.append(sorting != null ? "'" + sorting + "'" :
	// DatabaseSchema.TABLE_CATEGORY.COLUMN_SORTING);
	//
	// return buf.toString();
	// }

	// private static String appListToStr(ArrayList<App> applist) {
	// StringBuilder buf = new StringBuilder();
	//
	// for (App app : applist) {
	// buf.append(app.getId());
	// buf.append(",");
	// }
	//
	// String listStr = buf.toString();
	// MyLog.d(TAG, "list str: " + listStr);
	// if (listStr.endsWith(",")) {
	// // remove the last comma
	// listStr = listStr.substring(0, listStr.length() - 1);
	// }
	// MyLog.d(TAG, "list str: " + listStr);
	// return listStr;
	// }

	// private static void updateCategoryAppList(SQLiteDatabase db, String
	// cateid, String sorting,
	// int pageno, ArrayList<App> applist) {
	// StringBuilder buf = new StringBuilder();
	// buf.append("SELECT ");
	// buf.append(DatabaseSchema.TABLE_CATEGORY.COLUMN_APPLIST_NEW_FREE);
	// buf.append(" FROM ");
	// buf.append(DatabaseSchema.TABLE_CATEGORY.NAME);
	//
	// StringBuilder whereBuf = new StringBuilder();
	// whereBuf.append(DatabaseSchema.TABLE_CATEGORY.COLUMN_CATEID);
	// whereBuf.append(" = ");
	// whereBuf.append(cateid);
	// if (sorting != null) {
	// whereBuf.append(" AND ");
	// whereBuf.append(DatabaseSchema.TABLE_CATEGORY.COLUMN_SORTING);
	// whereBuf.append(" = '");
	// whereBuf.append(sorting + "'");
	// }
	//
	// buf.append(" WHERE ");
	// buf.append(whereBuf);
	//
	// Cursor c = query(db, buf.toString());
	// if (!c.moveToFirst()) {
	// throw new RuntimeException("no result for query > " + buf);
	// }
	//
	// String applistStr = c.getString(0);
	// if ("".equals(applistStr)) {
	// // no previous app list, update it
	// if (pageno != 0) {
	// throw new RuntimeException("trying to insert page " + pageno +
	// " for a non-initiated category " + cateid);
	// }
	//
	// ContentValues cv = new ContentValues();
	// cv.put(DatabaseSchema.TABLE_CATEGORY.COLUMN_APPLIST_NEW_FREE,
	// appListToStr(applist));
	//
	// db.update(DatabaseSchema.TABLE_CATEGORY.NAME, cv, whereBuf.toString(),
	// null);
	// } else {
	// // TODO: has previous data, insert to the right place
	// if (applistStr.split(",").length < (pageno *
	// Integer.parseInt(ConstantValues.NUM_PER_PAGE))) {
	// // new page, append to the end
	// } else {
	// // existed page
	// }
	// }
	//
	// c.close();
	// }

	/**
	 * Insert the app information to table.
	 * 
	 * @param db
	 * @param app
	 */
	public static void insertOrUpdateOneApp(SQLiteDatabase db, App app,
			boolean isInsert) {
		ContentValues cv = new ContentValues();
		cv.put(DatabaseSchema.TABLE_APP.COLUMN_APPID, app.getId());
		cv.put(DatabaseSchema.TABLE_APP.COLUMN_AUTHOR, app.getAuthorName());
		cv.put(DatabaseSchema.TABLE_APP.COLUMN_DESCRIP, app.getDescription());
		cv.put(DatabaseSchema.TABLE_APP.COLUMN_DOWNLOAD_CNT,
				app.getDownloadCount());
		cv.put(DatabaseSchema.TABLE_APP.COLUMN_DOWNLOAD_PATH,
				app.getDownloadPath());
		cv.put(DatabaseSchema.TABLE_APP.COLUMN_DOWNLOAD_ID, app.getDownloadId());
		cv.put(DatabaseSchema.TABLE_APP.COLUMN_ICON_URL, app.getIconUrl());
		cv.put(DatabaseSchema.TABLE_APP.COLUMN_INT_VERSION,
				app.getInfoVersion());
		// TODO: miss my_rating value here
		cv.put(DatabaseSchema.TABLE_APP.COLUMN_NAME, app.getName());
		cv.put(DatabaseSchema.TABLE_APP.COLUMN_PRICE, app.getPrice());
		cv.put(DatabaseSchema.TABLE_APP.COLUMN_PACKAGENAME, app.getPackgeName());
		MyLog.d(TAG, "PACKAGENAME: " + app.getPackgeName());
		cv.put(DatabaseSchema.TABLE_APP.COLUMN_LOCALPATH, app.getLocalPath());
		cv.put(DatabaseSchema.TABLE_APP.COLUMN_SCORE, app.getScore());
		cv.put(DatabaseSchema.TABLE_APP.COLUMN_SCORE_CNT, app.getScoreCount());
		cv.put(DatabaseSchema.TABLE_APP.COLUMN_COMMENT_CNT,
				app.getCommentCount());
		cv.put(DatabaseSchema.TABLE_APP.COLUMN_LANGUAGE, app.getLanguage());
		cv.put(DatabaseSchema.TABLE_APP.COLUMN_STATUS, app.getStatus());
		cv.put(DatabaseSchema.TABLE_APP.COLUMN_DOWNLOADED_SIZE,
				app.getDownloadedSize());
		cv.put(DatabaseSchema.TABLE_APP.COLUMN_SCREENSHOT_URL,
				app.getScreenshotUrl());
		// cv.put(DatabaseSchema.TABLE_APP.COLUMN_SLOGAN, app.getSlogan());
		cv.put(DatabaseSchema.TABLE_APP.COLUMN_SIZE, app.getSize());
		cv.put(DatabaseSchema.TABLE_APP.COLUMN_VERSION, app.getAppVersion());
		cv.put(DatabaseSchema.TABLE_APP.COLUMN_VERSION_NUM, app.getVersion());
		cv.put(DatabaseSchema.TABLE_APP.COLUMN_LAST_UPDATE_TIME,
				GeneralUtil.getUpdateTime());

		try {
			db.replaceOrThrow(DatabaseSchema.TABLE_APP.NAME, null, cv);
			// if (isInsert) {
			// db.insertOrThrow(DatabaseSchema.TABLE_APP.NAME,
			// null,
			// cv);
			// } else
			// {
			// db.update(DatabaseSchema.TABLE_APP.NAME,
			// cv,
			// DatabaseSchema.TABLE_APP.COLUMN_APPID + "=?",
			// new String[] {app.getId()});
			// }
		} catch (SQLException e) {
			Log.e(TAG,
					"insertOrUpdateOneApp,replaceOrThrow error=" + e.toString());
		}
	}

	// update one app unstable info
	public static void updateOneAppState(SQLiteDatabase db, App app) {
		ContentValues cv = new ContentValues();
		cv.put(DatabaseSchema.TABLE_APP.COLUMN_APPID, app.getId());
		cv.put(DatabaseSchema.TABLE_APP.COLUMN_DOWNLOAD_CNT,
				app.getDownloadCount());
		cv.put(DatabaseSchema.TABLE_APP.COLUMN_INT_VERSION,
				app.getInfoVersion());
		cv.put(DatabaseSchema.TABLE_APP.COLUMN_SCORE, app.getScore());
		cv.put(DatabaseSchema.TABLE_APP.COLUMN_SCORE_CNT, app.getScoreCount());
		cv.put(DatabaseSchema.TABLE_APP.COLUMN_COMMENT_CNT,
				app.getCommentCount());
		cv.put(DatabaseSchema.TABLE_APP.COLUMN_LAST_UPDATE_TIME,
				GeneralUtil.getUpdateTime());

		try {
			db.update(DatabaseSchema.TABLE_APP.NAME, cv,
					DatabaseSchema.TABLE_APP.COLUMN_APPID + "=?",
					new String[] { app.getId() + "" });
		} catch (Exception e) {
			Log.e(TAG, e.toString());
		}
	}

	public static final int THIS_IS_NEW_APP = 1;
	public static final int KEEP_APP_STATUS = 2;
	public static final int APP_NEED_UPDATE = 3;

	public static int checkAppStatus(SQLiteDatabase db, App app) {
		int result = KEEP_APP_STATUS;

		StringBuilder buf = new StringBuilder();
		buf.append("SELECT " + DatabaseSchema.TABLE_APP.COLUMN_STATUS + ","
				+ DatabaseSchema.TABLE_APP.COLUMN_VERSION_NUM);
		buf.append(" FROM " + DatabaseSchema.TABLE_APP.NAME);
		buf.append(" WHERE " + DatabaseSchema.TABLE_APP.COLUMN_APPID);
		buf.append("=" + app.getId());

		Cursor c = db.rawQuery(buf.toString(), null);

		boolean hasApp = (c != null && c.getCount() > 0 && c.moveToFirst());

		if (hasApp) {
			// get installed app version to check is need update
			int version = c
					.getInt(c
							.getColumnIndexOrThrow(DatabaseSchema.TABLE_APP.COLUMN_VERSION_NUM));
			int status = c
					.getInt(c
							.getColumnIndexOrThrow(DatabaseSchema.TABLE_APP.COLUMN_STATUS));
			if (status == App.INSTALLED) {
				if (version < app.getVersion()) {
					result = APP_NEED_UPDATE;
				}
			}
		} else
			result = THIS_IS_NEW_APP;

		c.close();

		return result;
	}

	public static boolean hasRecommend(SQLiteDatabase db, int recommendId) {
		StringBuilder buf = new StringBuilder();
		buf.append("SELECT "
				+ DatabaseSchema.TABLE_RECOMMEND.COLUMN_RECOMMENDID);
		buf.append(" FROM " + DatabaseSchema.TABLE_RECOMMEND.NAME);
		buf.append(" WHERE "
				+ DatabaseSchema.TABLE_RECOMMEND.COLUMN_RECOMMENDID);
		buf.append("=" + recommendId);

		Cursor c = db.rawQuery(buf.toString(), null);

		boolean hasRecommend = (c != null && c.getCount() > 0 && c
				.moveToFirst());

		c.close();

		return hasRecommend;
	}

	public static boolean isAppInLocalDB(SQLiteDatabase db, App app) {

		StringBuilder buf = new StringBuilder();
		buf.append("SELECT " + DatabaseSchema.TABLE_APP.COLUMN_STATUS + ","
				+ DatabaseSchema.TABLE_APP.COLUMN_VERSION_NUM);
		buf.append(" FROM " + DatabaseSchema.TABLE_APP.NAME);
		buf.append(" WHERE " + DatabaseSchema.TABLE_APP.COLUMN_APPID);
		buf.append("=" + app.getId());

		Log.e("1231238", "");
		Cursor c = db.rawQuery(buf.toString(), null);

		boolean hasApp = (c != null && c.getCount() > 0 && c.moveToFirst());
		c.close();

		return hasApp;
	}

	private static boolean isDetailChanged(SQLiteDatabase db, App app) {
		StringBuilder buf = new StringBuilder();
		buf.append("SELECT " + DatabaseSchema.TABLE_APP.COLUMN_APPID);
		buf.append(" FROM " + DatabaseSchema.TABLE_APP.NAME);
		buf.append(" WHERE " + DatabaseSchema.TABLE_APP.COLUMN_APPID);
		Log.e("1231239", "");
		buf.append("=" + app.getId());
		buf.append(" AND " + DatabaseSchema.TABLE_APP.COLUMN_INT_VERSION);
		buf.append("=" + app.getInfoVersion());

		Cursor c = db.rawQuery(buf.toString(), null);

		boolean isDetailChanged = !(c != null && c.getCount() > 0);

		c.close();

		return isDetailChanged;
	}

	public static Cursor getAppListCursor() {
		// TODO Auto-generated method stub
		return null;
	}

	/**
	 * Save app list for specified category. This will erase any previous app
	 * list of this category.
	 * 
	 * This function will also save the app informations in app table.
	 * 
	 * @param db
	 *            Database instance
	 * @param cateid
	 *            category id
	 * @param sorting
	 *            sorting, null if no sorting.
	 * @param applist
	 *            app list
	 */
	// public static void saveAppList(SQLiteDatabase db, String cateid, String
	// sorting, ArrayList<App> applist) {
	// saveAppList(db, cateid, sorting, 0, applist);
	// }

	/**
	 * Get app list for specific category.
	 * 
	 * @param db
	 * @param cateid
	 * @param sorting
	 * @return the app list
	 */
	// public static ArrayList<App> getAppList(SQLiteDatabase db, String cateid,
	// String sorting) {
	// String where = getQueryCategoryWithSorting(cateid, sorting, true);
	//
	// StringBuilder buf = new StringBuilder();
	// buf.append("SELECT ");
	// buf.append(DatabaseSchema.TABLE_CATEGORY.COLUMN_APPLIST_NEW_FREE);
	// buf.append(" FROM ");
	// buf.append(DatabaseSchema.TABLE_CATEGORY.NAME);
	// buf.append(where);
	//
	// Cursor c = query(db, buf.toString());
	// MyLog.d(TAG, "cursor count: " + c.getCount());
	// ArrayList<App> appList = new ArrayList<App>();
	// if (c.moveToFirst()) {
	// MyLog.d(TAG, "move to first success");
	// String applistStr = c.getString(0);
	//
	// MyLog.d(TAG, "app list str > " + applistStr);
	// if (!"".equals(applistStr)) {
	// for (String id : applistStr.split(",")) {
	// appList.add(queryApp(db, id));
	// }
	// }
	// }
	//
	// c.close();
	// return appList;
	// }

	/**
	 * Get app id list for specific category.
	 * 
	 * @param db
	 * @param cateid
	 * @param sorting
	 * @return the app list
	 */
	public static String getAppIdList(SQLiteDatabase db, String cateid,
			String sorting) {

		StringBuilder buf = new StringBuilder();
		buf.append("SELECT ");
		buf.append(sorting);
		buf.append(" FROM ");
		buf.append(DatabaseSchema.TABLE_CATEGORY.NAME);
		buf.append(" WHERE ");
		buf.append(DatabaseSchema.TABLE_CATEGORY.COLUMN_CATEID);
		buf.append(" = ");
		buf.append(cateid);

		Cursor c = query(db, buf.toString());
		MyLog.d(TAG, "cursor count: " + c.getCount());

		if (c.moveToFirst()) {
			MyLog.d(TAG, "move to first success");
			String applistStr = c.getString(0);

			MyLog.d(TAG, "app list str > " + applistStr);

			c.close();
			return applistStr;
		}

		c.close();
		return null;
	}

	// /**
	// * Get app id list for specific category. in rank table
	// *
	// * @param db
	// * @param cateid
	// * @param sorting
	// * @return the app list
	// */
	// public static String getAppIdListInRank(SQLiteDatabase db, String rankid,
	// String rankTime) {
	//
	// StringBuilder buf = new StringBuilder();
	// buf.append("SELECT ");
	// buf.append(rankTime);
	// buf.append(" FROM ");
	// buf.append(DatabaseSchema.TABLE_RANK.NAME);
	// buf.append(" WHERE ");
	// buf.append(DatabaseSchema.TABLE_RANK.COLUMN_RANKID);
	// buf.append(" = ");
	// buf.append(rankid);
	//
	// Cursor c = query(db, buf.toString());
	// MyLog.d(TAG, "cursor count: " + c.getCount());
	//
	// if (c.moveToFirst()) {
	// MyLog.d(TAG, "move to first success");
	// String applistStr = c.getString(0);
	//
	// MyLog.d(TAG, "app list str > " + applistStr);
	//
	// c.close();
	// return applistStr;
	// }
	//
	// c.close();
	// return null;
	// }

	/**
	 * Get app id list in recommend table
	 * 
	 * @param db
	 * @return the app list
	 */
	public static String getAppIdListInRecommend(SQLiteDatabase db) {

		StringBuilder buf = new StringBuilder();
		buf.append("SELECT ");
		buf.append(DatabaseSchema.TABLE_RECOMMEND.COLUMN_TARGET_ID);
		buf.append(" FROM ");
		buf.append(DatabaseSchema.TABLE_RECOMMEND.NAME);

		Cursor c = query(db, buf.toString());
		MyLog.d(TAG, "cursor count: " + c.getCount());

		String applistStr = null;
		if (c != null && c.getCount() > 0) {
			while (c.moveToNext()) {

				if (applistStr == null)
					applistStr = c.getString(0);
				else
					applistStr += "," + c.getString(0);

			}
		}

		c.close();

		MyLog.d(TAG, "app list str > " + applistStr);

		return applistStr;
	}

	public static String getNewestRecommendId(SQLiteDatabase db) {
		StringBuilder buf = new StringBuilder();
		buf.append("SELECT MAX(");
		buf.append(DatabaseSchema.TABLE_RECOMMEND.COLUMN_RECOMMENDID);
		buf.append(") FROM ");
		buf.append(DatabaseSchema.TABLE_RECOMMEND.NAME);
		// buf.append(" ORDER BY ");
		// buf.append(DatabaseSchema.TABLE_RECOMMEND.COLUMN_RECOMMENDID);
		// buf.append(" DESC LIMIT 1");

		MyLog.d(TAG, "buf.toString() " + buf.toString());

		Cursor c = db.rawQuery(buf.toString(), null);
		String newestRecommendId = null;
		if (c.moveToFirst()) {
			MyLog.d(TAG, "move to first success");
			newestRecommendId = c.getString(0);

			MyLog.d(TAG, "newestRecommendId > " + newestRecommendId);
		} else
			newestRecommendId = "0";

		c.close();
		return newestRecommendId;
	}

	public static String getRecommendId(SQLiteDatabase db) {
		StringBuilder buf = new StringBuilder();
		buf.append("SELECT ");
		buf.append(DatabaseSchema.TABLE_RECOMMEND.COLUMN_RECOMMENDID);
		buf.append(" FROM ");
		buf.append(DatabaseSchema.TABLE_RECOMMEND.NAME);

		Cursor c = db.rawQuery(buf.toString(), null);

		String newestRecommendId = null;
		if (c != null && c.getCount() > 0) {
			while (c.moveToNext()) {

				newestRecommendId = c.getString(0);

				MyLog.d(TAG, "newestRecommendId > " + newestRecommendId);

			}
		}

		c.close();
		return newestRecommendId;
	}

	public static String getAllAppIdList(Context ctx) {

		SQLiteDatabase db = new DatabaseHelper(ctx).getWritableDatabase();

		StringBuilder buf = new StringBuilder();
		buf.append("SELECT ");
		buf.append(DatabaseSchema.TABLE_APP.COLUMN_APPID);
		buf.append(" FROM ");
		buf.append(DatabaseSchema.TABLE_APP.NAME);
		Log.e("------------", "333------------");
		Cursor c = query(db, buf.toString());
		MyLog.d(TAG, "cursor count: " + c.getCount());

		if (c.moveToFirst()) {
			StringBuilder applist = new StringBuilder();
			applist.append(c.getString(0) + ",");
			while (c.moveToNext()) {
				applist.append(c.getString(0) + ",");
			}

			MyLog.d(TAG, "app list str > " + applist.toString());

			c.close();
			db.close();
			return applist.toString();
		}

		c.close();
		db.close();
		return null;
	}

	public static String getUncachedAppList(SQLiteDatabase db, String applist) {

		String[] applistArray = applist.split(",");
		StringBuilder buf = new StringBuilder();
		buf.append("SELECT ");
		buf.append(DatabaseSchema.TABLE_APP.COLUMN_APPID);
		buf.append(" FROM ");
		buf.append(DatabaseSchema.TABLE_APP.NAME);
		buf.append(" WHERE ");
		Log.e("1231235===============", "===================");
		for (int i = 0; i < applistArray.length; i++) {
			buf.append(DatabaseSchema.TABLE_APP.COLUMN_APPID);
			buf.append(" = '");
			buf.append(applistArray[i]);
			buf.append("'");

			if (i != applistArray.length - 1)
				buf.append(" OR ");
		}

//		if (buf.lastIndexOf("60") == -1) {
//			Log.e("1231235===============", "==========throw==========");
//			throw new RuntimeException(
//					"Can't get proper result for applistArray " + applistArray);
//		}
		Cursor c = query(db, buf.toString());

		MyLog.d(TAG, "cursor count: " + c.getCount());

		if (c.getCount() == applistArray.length) {
			c.close();
			return null;
		}

		if (!c.moveToFirst()) {
			c.close();
			return applist;
		}

		StringBuilder cachedApplist = new StringBuilder();
		cachedApplist.append(c.getString(0) + ",");
		while (c.moveToNext()) {
			cachedApplist.append(c.getString(0) + ",");
		}

		c.close();

		String cachedApplistString = cachedApplist.toString();

		StringBuilder uncachedApplist = new StringBuilder();
		for (int i = 0; i < applistArray.length; i++) {
			if (cachedApplistString.indexOf(applistArray[i]) == -1) {
				if (uncachedApplist.length() == 0)
					uncachedApplist.append(applistArray[i]);
				else
					uncachedApplist.append("," + applistArray[i]);
			}
		}

		MyLog.d(TAG,
				"uncachedApplist app list str > " + uncachedApplist.toString());
		return uncachedApplist.toString();
	}

	public static String getCacheAppList(SQLiteDatabase db, String applist) {

		String[] applistArray = applist.split(",");
		StringBuilder buf = new StringBuilder();
		buf.append("SELECT ");
		buf.append(DatabaseSchema.TABLE_APP.COLUMN_APPID);
		buf.append(" FROM ");
		buf.append(DatabaseSchema.TABLE_APP.NAME);
		buf.append(" WHERE ");
		Log.e("1231234=====================", "====================");
		for (int i = 0; i < applistArray.length; i++) {
			buf.append(DatabaseSchema.TABLE_APP.COLUMN_APPID);
			buf.append(" = '");
			buf.append(applistArray[i]);
			buf.append("'");

			if (i != applistArray.length - 1)
				buf.append(" OR ");
		}

		Cursor c = query(db, buf.toString());

		MyLog.d(TAG, "cursor count: " + c.getCount());

		if (c.getCount() == applistArray.length) {
			c.close();
			return null;
		}

		if (!c.moveToFirst()) {
			c.close();
			return applist;
		}

		StringBuilder cachedApplist = new StringBuilder();
		cachedApplist.append(c.getString(0) + ",");
		while (c.moveToNext()) {
			cachedApplist.append(c.getString(0) + ",");
		}

		c.close();

		MyLog.d(TAG, "cachedApplist app list str > " + cachedApplist.toString());
		return cachedApplist.toString();
	}

	public static ArrayList<App> getAppList(SQLiteDatabase db,
			String applistString) {
		ArrayList<App> appList = new ArrayList<App>();

		MyLog.d(TAG, "getAppList > " + applistString);
		if (!"".equals(applistString)) {
			for (String id : applistString.split(",")) {
				appList.add(getAppById(db, Integer.parseInt(id)));
			}
		}

		return appList;
	}

	// private static App queryApp(SQLiteDatabase db, String id) {
	// Cursor c = db.query(DatabaseSchema.TABLE_APP.NAME,
	// new String[]{"*"},
	// DatabaseSchema.TABLE_APP.COLUMN_APPID + " =?",
	// new String[]{id}, null, null, null);
	//
	// App app = null;
	// if (c.moveToFirst()) {
	// app = createAppFromCursor(c);
	// }
	//
	// c.close();
	// return app;
	// }

	// private static String getQueryCategoryWithSorting(String cateid, String
	// sorting,
	// boolean needWhereClause) {
	// StringBuilder buf = new StringBuilder();
	// if (needWhereClause) buf.append(" WHERE ");
	// buf.append(DatabaseSchema.TABLE_CATEGORY.COLUMN_CATEID);
	// buf.append(" = '");
	// buf.append(cateid);
	// buf.append("'");
	// if (sorting != null) {
	// buf.append(" AND ");
	// buf.append(DatabaseSchema.TABLE_CATEGORY.COLUMN_SORTING);
	// buf.append(" = '");
	// buf.append(sorting);
	// buf.append("'");
	// }
	//
	// return buf.toString();
	// }

	public static String getLocalPathFromUrl(String url) {
		if (url == null)
			return null;

		String path = url.substring(url.lastIndexOf('/') + 1).trim();

		if (path == null || path.equals(""))
			return null;
		else
			return (GeneralUtil.LOCAL_DOWNLOAD_IMAGE_PATH + url.substring(
					url.lastIndexOf('/') + 1).trim());
	}

	public static Bitmap bitmapIcon;
	public synchronized static Bitmap getImage(String iconPath) {
		bitmapIcon = null;
		bitmapIcon = BitmapFactory.decodeFile(iconPath);
		return bitmapIcon;
	}

	public synchronized static void saveImage(Context ctx, String url, Bitmap bm) {
		// SQLiteDatabase db = new DatabaseHelper(ctx).getWritableDatabase();

		// save as file first
		String dir = GeneralUtil.LOCAL_DOWNLOAD_IMAGE_PATH;
		File d = new File(dir);
		if (!d.exists()) {
			d.mkdir();
		}

		String filename = GeneralUtil.LOCAL_DOWNLOAD_IMAGE_PATH
				+ url.substring(url.lastIndexOf('/') + 1);
		MyLog.d(TAG, "save " + url + "as file: " + filename);
		File f = new File(filename);
		if (!f.exists())
			try {
				f.createNewFile();
			} catch (IOException e1) {
				MyLog.e(TAG, "can't create file: " + f, e1);
			}

		try {
			FileOutputStream out = new FileOutputStream(f);

			bm.compress(Bitmap.CompressFormat.PNG, 90, out);
			// bm.compress(Bitmap.CompressFormat.JPEG, 100, out);

		} catch (FileNotFoundException e) {
			MyLog.e(TAG, "Can't save file to " + f);
		}

		// db.close();
	}
 
	public static App getAppByPackageName(SQLiteDatabase db, String packageName) {
		StringBuilder buf = new StringBuilder();
		buf.append("SELECT * FROM " + DatabaseSchema.TABLE_APP.NAME);
		buf.append(" WHERE " + DatabaseSchema.TABLE_APP.COLUMN_PACKAGENAME);
		buf.append("='" + packageName + "'");

		Cursor c = db.rawQuery(buf.toString(), null);
		if (c == null || c.getCount() == 0 || !c.moveToFirst()) {
			MyLog.e(TAG, "Can't get result for " + buf);

			if (c != null)
				c.close();

			return null;
		}

		App app = createAppFromCursor(c);

		c.close();

		return app;
	}

	public static Boolean needUploadToServer(SQLiteDatabase db,
			String packageName, String version) {
		StringBuilder buf = new StringBuilder();
		buf.append("SELECT " + DatabaseSchema.TABLE_APP.COLUMN_APPID + ","
				+ DatabaseSchema.TABLE_APP.COLUMN_VERSION_NUM + ","
				+ DatabaseSchema.TABLE_APP.COLUMN_STATUS);
		buf.append(" FROM ");
		buf.append(DatabaseSchema.TABLE_APP.NAME);
		buf.append(" WHERE " + DatabaseSchema.TABLE_APP.COLUMN_PACKAGENAME);
		buf.append("='" + packageName + "'");

		Log.e("12312310", "");
		MyLog.d(TAG, "packageName: " + packageName);

		Cursor c = db.rawQuery(buf.toString(), null);
		if (c == null || c.getCount() == 0 || !c.moveToFirst()) {
			if (c != null)
				c.close();

			MyLog.d(TAG, "No this app!!!");
			return true;
		}

		int versionInDb = c
				.getInt(c
						.getColumnIndexOrThrow(DatabaseSchema.TABLE_APP.COLUMN_VERSION_NUM));
		int appid = c.getInt(c
				.getColumnIndexOrThrow(DatabaseSchema.TABLE_APP.COLUMN_APPID));
		int status = c.getInt(c
				.getColumnIndexOrThrow(DatabaseSchema.TABLE_APP.COLUMN_STATUS));

		c.close();

		if (Integer.parseInt(version) > versionInDb) {
			// change app status
			updateAppStatus(db, appid, App.INSTALLED);

			return true;
		} else if (Integer.parseInt(version) < versionInDb) {
			// change app status
			updateAppStatus(db, appid, App.HAS_UPDATE);
			return false;
		} else {
			// Integer.parseInt(version) == versionInDb
			if (status != App.INSTALLED)
				updateAppStatus(db, appid, App.INSTALLED);

			return false;
		}

	}

	public static void updateAppStatus(SQLiteDatabase db, int appid,
			int appStatus) {
		try {
			StringBuilder buf = new StringBuilder();
			buf.append(DatabaseSchema.TABLE_APP.COLUMN_APPID);
			buf.append("=" + appid);
			Log.e("12312312======0", "============0");

			ContentValues cv = new ContentValues();
			cv.put(DatabaseSchema.TABLE_APP.COLUMN_STATUS, appStatus);

			db.update(DatabaseSchema.TABLE_APP.NAME, cv, buf.toString(), null);
		} catch (Exception e) {
			Log.e(TAG, e.toString());
		}
	}

	public static void removeDeletedApp() {
		// TODO Auto-generated method stub

	}

	public static void updateDownloadErrorStatus(int appDbId) {
		// TODO Auto-generated method stub

	}

	public static void resetAppToInit(Context ctx, int appDbId) {

		SQLiteDatabase db = null;
		try {
			db = new DatabaseHelper(ctx).getWritableDatabase();

			ContentValues cv = new ContentValues();
			cv.put(DatabaseSchema.TABLE_APP.COLUMN_STATUS, App.INIT);
			cv.put(DatabaseSchema.TABLE_APP.COLUMN_DOWNLOADED_SIZE, 0);

			db.update(DatabaseSchema.TABLE_APP.NAME, cv,
					DatabaseSchema.TABLE_APP.COLUMN_ID + "=" + appDbId, null);

			db.close();
		} catch (Exception e) {
			if (db != null)
				db.close();

			Log.e(TAG, e.toString());
		}

	}

	public static void updateDownloadPauseStatus(Context ctx, int appDbId,
			int downloadedSize) {

		SQLiteDatabase db = null;
		try {
			db = new DatabaseHelper(ctx).getWritableDatabase();

			ContentValues cv = new ContentValues();
			cv.put(DatabaseSchema.TABLE_APP.COLUMN_STATUS, App.PAUSED);
			cv.put(DatabaseSchema.TABLE_APP.COLUMN_DOWNLOADED_SIZE,
					downloadedSize);

			db.update(DatabaseSchema.TABLE_APP.NAME, cv,
					DatabaseSchema.TABLE_APP.COLUMN_ID + "=" + appDbId, null);

			db.close();
		} catch (Exception e) {
			if (db != null)
				db.close();

			Log.e(TAG, e.toString());
		}

	}

	public static void updateDownloadingStatus(Context ctx, int appDbId,
			int downloadedSize) {
		SQLiteDatabase db = null;
		try {
			db = new DatabaseHelper(ctx).getWritableDatabase();

			ContentValues cv = new ContentValues();
			cv.put(DatabaseSchema.TABLE_APP.COLUMN_STATUS, App.DOWNLOADING);
			cv.put(DatabaseSchema.TABLE_APP.COLUMN_DOWNLOADED_SIZE,
					downloadedSize);

			db.update(DatabaseSchema.TABLE_APP.NAME, cv,
					DatabaseSchema.TABLE_APP.COLUMN_ID + "=" + appDbId, null);

			db.close();
		} catch (Exception e) {
			if (db != null)
				db.close();

			Log.e(TAG, e.toString());
		}

	}

	public static void updateDownloadResumeStatus(int appDbId) {
		// TODO Auto-generated method stub

	}

	public static void updateDownloadCancelStatus(Context ctx, int appDbId) {

		SQLiteDatabase db = null;

		try {
			db = new DatabaseHelper(ctx).getWritableDatabase();

			ContentValues cv = new ContentValues();
			cv.put(DatabaseSchema.TABLE_APP.COLUMN_STATUS, App.INIT);

			db.update(DatabaseSchema.TABLE_APP.NAME, cv,
					DatabaseSchema.TABLE_APP.COLUMN_ID + "=" + appDbId, null);

			db.close();
		} catch (Exception e) {
			if (db != null)
				db.close();

			Log.e(TAG, e.toString());
		}
	}

	public static void updateDownloadCompleteStatus(Context ctx, int appDbId,
			String localPath, String packageName, int appSize, int downloadCount) {
		MyLog.d(TAG, "Download complete >> " + appDbId);

		SQLiteDatabase db = null;
		try {
			db = new DatabaseHelper(ctx).getWritableDatabase();

			ContentValues cv = new ContentValues();
			cv.put(DatabaseSchema.TABLE_APP.COLUMN_STATUS, App.DOWNLOADED);
			cv.put(DatabaseSchema.TABLE_APP.COLUMN_PACKAGENAME, packageName);
			cv.put(DatabaseSchema.TABLE_APP.COLUMN_LOCALPATH, localPath);
			cv.put(DatabaseSchema.TABLE_APP.COLUMN_DOWNLOAD_CNT, downloadCount);

			db.update(DatabaseSchema.TABLE_APP.NAME, cv,
					DatabaseSchema.TABLE_APP.COLUMN_ID + "=" + appDbId, null);

			db.close();
		} catch (Exception e) {
			if (db != null)
				db.close();

			Log.e(TAG, e.toString());
		}
	}

	public static void updateAppCommentCountCnt(SQLiteDatabase db, int appId,
			int commentCount) {
		MyLog.d(TAG, "comment complete >> " + appId);

		ContentValues cv = new ContentValues();

		cv.put(DatabaseSchema.TABLE_APP.COLUMN_COMMENT_CNT, commentCount);

		try {
			db.update(DatabaseSchema.TABLE_APP.NAME, cv,
					DatabaseSchema.TABLE_APP.COLUMN_APPID + "=" + appId, null);

			db.close();
		} catch (Exception e) {
			Log.e(TAG, e.toString());
		}
	}

	public static void updateAppScoreCnt(SQLiteDatabase db, int appId,
			int score, int scoreCount) {
		MyLog.d(TAG, "comment complete >> " + appId);

		ContentValues cv = new ContentValues();

		cv.put(DatabaseSchema.TABLE_APP.COLUMN_SCORE, score);
		cv.put(DatabaseSchema.TABLE_APP.COLUMN_SCORE_CNT, scoreCount);

		try {
			db.update(DatabaseSchema.TABLE_APP.NAME, cv,
					DatabaseSchema.TABLE_APP.COLUMN_APPID + "=" + appId, null);

			db.close();
		} catch (Exception e) {
			Log.e(TAG, e.toString());
		}
	}

	public static void updateAppCommentCnt(SQLiteDatabase db, int appId,
			int commentCount) {
		MyLog.d(TAG, "comment complete >> " + appId);

		ContentValues cv = new ContentValues();

		cv.put(DatabaseSchema.TABLE_APP.COLUMN_COMMENT_CNT, commentCount);

		try {
			db.update(DatabaseSchema.TABLE_APP.NAME, cv,
					DatabaseSchema.TABLE_APP.COLUMN_APPID + "=" + appId, null);

			db.close();
		} catch (Exception e) {
			Log.e(TAG, e.toString());
		}
	}

	public static void updateAppDownloadCnt(SQLiteDatabase db, int appId,
			int downloadCnt) {
		MyLog.d(TAG, "updateAppDownloadCnt >> " + appId);

		ContentValues cv = new ContentValues();

		cv.put(DatabaseSchema.TABLE_APP.COLUMN_DOWNLOAD_CNT, downloadCnt);

		try {
			db.update(DatabaseSchema.TABLE_APP.NAME, cv,
					DatabaseSchema.TABLE_APP.COLUMN_APPID + "=" + appId, null);

			db.close();
		} catch (Exception e) {
			Log.e(TAG, e.toString());
		}
	}

	public static App getAppById(Context ctx, int id) {
		MyLog.d(TAG, "get app by id via context >>>");
		SQLiteDatabase db = new DatabaseHelper(ctx).getWritableDatabase();

		App app = getAppById(db, id);

		db.close();
		return app;
	}

	public static App getAppById(SQLiteDatabase db, int appid) {
		MyLog.d(TAG, "get app by id > " + appid);
		// Cursor c = db.query(DatabaseSchema.TABLE_APP.NAME,
		// new String[] {"*"},
		// DatabaseSchema.TABLE_APP.COLUMN_APPID + "=?",
		// new String[] {appid},
		// null, null, null);
		StringBuilder buf = new StringBuilder();
		buf.append("SELECT * FROM ");
		buf.append(DatabaseSchema.TABLE_APP.NAME);
		buf.append(" WHERE ");
		buf.append(DatabaseSchema.TABLE_APP.COLUMN_APPID);
		buf.append("=");
		buf.append(appid);

//		if (buf.lastIndexOf("60") == -1) {
//			throw new RuntimeException("Can't get proper result for " + appid);
//		}

		Cursor c = db.rawQuery(buf.toString(), null);

		if (c == null) {
			throw new RuntimeException("Can't get proper result for " + appid);
		}

		if (c.getCount() != 1 || !c.moveToFirst()) {
			c.close();
			return null;
		}

		App app = createAppFromCursor(c);

		c.close();
		return app;
	}

	// public static void updateAppDownloadInfo(SQLiteDatabase db, String id,
	// String downloadPath,
	// String downloadId) {
	// ContentValues cv = new ContentValues();
	// cv.put(DatabaseSchema.TABLE_APP.COLUMN_DOWNLOAD_PATH, downloadPath);
	// cv.put(DatabaseSchema.TABLE_APP.COLUMN_DOWNLOAD_ID, downloadId);
	//
	// db.update(DatabaseSchema.TABLE_APP.NAME, cv,
	// DatabaseSchema.TABLE_APP.COLUMN_APPID + "=?",
	// new String[] {id});
	// }

	/**
	 * Query category list from database.
	 * 
	 * @param db
	 *            Database reference
	 * @param i
	 *            index of the headers, could be the following values: -> 0: app
	 *            categories -> 1: game categories -> 2: charts
	 * @return the category list
	 */
	public static ArrayList<Category> getCategoryList(Context ctx,
			SQLiteDatabase db, int i) {
		int parentid;
		switch (i) {
		case 0:
			parentid = 31;
			break;
		case 1:
			parentid = 6;
			break;
		case 2:
			parentid = 5;
			break;
		default:
			throw new RuntimeException("Unknow index: " + i);
		}

		// Cursor c = db.query(DatabaseSchema.TABLE_CATEGORY.NAME,
		// null,
		// DatabaseSchema.TABLE_CATEGORY.COLUMN_PARENT_ID + "=" + parentid,
		// null,
		// null,
		// null,
		// null);

		StringBuilder buf = new StringBuilder();
		buf.append("SELECT * FROM ");
		buf.append(DatabaseSchema.TABLE_CATEGORY.NAME);
		buf.append(" WHERE ");
		buf.append(DatabaseSchema.TABLE_CATEGORY.COLUMN_PARENT_ID);
		buf.append("=");
		buf.append(parentid + "");
		// if (i == 0)
		// {
		// buf.append(" ORDER BY ");
		// buf.append(DatabaseSchema.TABLE_CATEGORY.COLUMN_CATEID);
		// buf.append(" DESC ");
		// }
		buf.append(" ORDER BY ");
		buf.append(DatabaseSchema.TABLE_CATEGORY.COLUMN_CATE_ORDER);

		Cursor c = db.rawQuery(buf.toString(), null);

		ArrayList<Category> list = new ArrayList<Category>();
		if (c != null && c.getCount() > 0) {

			String cateShowList = GeneralUtil.getCateDisplayList(ctx);
			MyLog.d(TAG, "cateShowList:" + cateShowList);

			while (c.moveToNext()) {
				Category cate = new Category();
				cate.setIconUrl(c.getString(c
						.getColumnIndexOrThrow(DatabaseSchema.TABLE_CATEGORY.COLUMN_ICON_URL)));
				cate.setName(c.getString(c
						.getColumnIndexOrThrow(DatabaseSchema.TABLE_CATEGORY.COLUMN_NAME)));
				cate.setSig(c.getInt(c
						.getColumnIndexOrThrow(DatabaseSchema.TABLE_CATEGORY.COLUMN_CATEID)));
				cate.setAppCount(c.getInt(c
						.getColumnIndexOrThrow(DatabaseSchema.TABLE_CATEGORY.COLUMN_APPCOUNT)));
				cate.setDescription(c.getString(c
						.getColumnIndexOrThrow(DatabaseSchema.TABLE_CATEGORY.COLUMN_CATE_DESC)));
				cate.setType(c.getInt(c
						.getColumnIndexOrThrow(DatabaseSchema.TABLE_CATEGORY.COLUMN_TYPE)));
				cate.setUpdateInterval(c.getLong(c
						.getColumnIndexOrThrow(DatabaseSchema.TABLE_CATEGORY.COLUMN_UPDATE_INTERVAL)));
				cate.setCateOrder(c.getInt(c
						.getColumnIndexOrThrow(DatabaseSchema.TABLE_CATEGORY.COLUMN_CATE_ORDER)));
				if (cateShowList.indexOf(cate.getSig() + "") != -1)

					MyLog.d(TAG, "cate" + cate.getSig());

				if (cateShowList.indexOf("cate" + cate.getSig()) != -1) {
					MyLog.d(TAG, "Add cate" + cate.getSig());
					list.add(cate);
				}

			}
		}

		c.close();

		return list;
	}

	/**
	 * Query rank list from database.
	 * 
	 * @param db
	 *            Database reference
	 * @return the rank list
	 */
	public static ArrayList<Category> getRankList(SQLiteDatabase db) {
		StringBuilder buf = new StringBuilder();
		buf.append("SELECT * FROM ");
		buf.append(DatabaseSchema.TABLE_CATEGORY.NAME);
		buf.append(" WHERE ");
		buf.append(DatabaseSchema.TABLE_CATEGORY.COLUMN_TYPE);
		buf.append("=");
		buf.append(Category.CATE_TYPE_RANK);

		buf.append(" ORDER BY ");
		buf.append(DatabaseSchema.TABLE_CATEGORY.COLUMN_CATE_ORDER);

		MyLog.d(TAG, buf.toString());

		Cursor c = db.rawQuery(buf.toString(), null);

		ArrayList<Category> list = new ArrayList<Category>();
		if (c != null && c.getCount() > 0) {
			while (c.moveToNext()) {
				Category cate = new Category();
				cate.setIconUrl(c.getString(c
						.getColumnIndexOrThrow(DatabaseSchema.TABLE_CATEGORY.COLUMN_ICON_URL)));
				cate.setName(c.getString(c
						.getColumnIndexOrThrow(DatabaseSchema.TABLE_CATEGORY.COLUMN_NAME)));
				cate.setSig(c.getInt(c
						.getColumnIndexOrThrow(DatabaseSchema.TABLE_CATEGORY.COLUMN_CATEID)));
				cate.setAppCount(c.getInt(c
						.getColumnIndexOrThrow(DatabaseSchema.TABLE_CATEGORY.COLUMN_APPCOUNT)));
				cate.setDescription(c.getString(c
						.getColumnIndexOrThrow(DatabaseSchema.TABLE_CATEGORY.COLUMN_CATE_DESC)));
				cate.setType(c.getInt(c
						.getColumnIndexOrThrow(DatabaseSchema.TABLE_CATEGORY.COLUMN_TYPE)));
				cate.setUpdateInterval(c.getLong(c
						.getColumnIndexOrThrow(DatabaseSchema.TABLE_CATEGORY.COLUMN_UPDATE_INTERVAL)));
				cate.setCateOrder(c.getInt(c
						.getColumnIndexOrThrow(DatabaseSchema.TABLE_CATEGORY.COLUMN_CATE_ORDER)));
				list.add(cate);
			}
		}

		c.close();

		return list;
	}

	/**
	 * Query recommend list from database.
	 * 
	 * @param db
	 *            Database reference
	 * @return the recommend list
	 */
	public static ArrayList<Recommend> getRecommendList(Context ctx,
			SQLiteDatabase db) {

		String list = GeneralUtil.getRecommendDisplayList(ctx);

		MyLog.d(TAG, "list " + list);

		if (list == null)
			return null;

		String[] displayList = list.split(",");

		ArrayList<Recommend> displayRecommend = new ArrayList<Recommend>();

		for (String recommendId : displayList) {
			StringBuilder buf = new StringBuilder();
			buf.append("SELECT * FROM ");
			buf.append(DatabaseSchema.TABLE_RECOMMEND.NAME);
			buf.append(" WHERE ");
			buf.append(DatabaseSchema.TABLE_RECOMMEND.COLUMN_RECOMMENDID);
			buf.append("=");
			buf.append(recommendId);

			Cursor c = db.rawQuery(buf.toString(), null);

			MyLog.d(TAG, "recommendId " + recommendId);

			if (c.moveToFirst()) {
				MyLog.d(TAG, "add an recommend " + recommendId);

				Recommend recommend = new Recommend();
				recommend
						.setId(c.getInt(c
								.getColumnIndexOrThrow(DatabaseSchema.TABLE_RECOMMEND.COLUMN_RECOMMENDID)));
				recommend
						.setName(c.getString(c
								.getColumnIndexOrThrow(DatabaseSchema.TABLE_RECOMMEND.COLUMN_NAME)));
				recommend
						.setDesc(c.getString(c
								.getColumnIndexOrThrow(DatabaseSchema.TABLE_RECOMMEND.COLUMN_RECOMMEND_DESC)));
				recommend
						.setTargetType(c.getInt(c
								.getColumnIndexOrThrow(DatabaseSchema.TABLE_RECOMMEND.COLUMN_TARGET_TYPE)));
				recommend
						.setTargetId(c.getString(c
								.getColumnIndexOrThrow(DatabaseSchema.TABLE_RECOMMEND.COLUMN_TARGET_ID)));
				recommend
						.setIconUrl(c.getString(c
								.getColumnIndexOrThrow(DatabaseSchema.TABLE_RECOMMEND.COLUMN_ICON_URL)));
				recommend
						.setImageUrl(c.getString(c
								.getColumnIndexOrThrow(DatabaseSchema.TABLE_RECOMMEND.COLUMN_IMAGEA_URL)));
				recommend
						.setDate(c.getString(c
								.getColumnIndexOrThrow(DatabaseSchema.TABLE_RECOMMEND.COLUMN_DATE)));

				displayRecommend.add(recommend);
			}

			c.close();
		}

		return displayRecommend;
	}

	public static Category getCategory(SQLiteDatabase db, int cateId) {

		StringBuilder buf = new StringBuilder();
		buf.append("SELECT * FROM ");
		buf.append(DatabaseSchema.TABLE_CATEGORY.NAME);
		buf.append(" WHERE ");
		buf.append(DatabaseSchema.TABLE_CATEGORY.COLUMN_CATEID);
		buf.append("=");
		buf.append(cateId);

		Cursor c = db.rawQuery(buf.toString(), null);

		if (c != null && c.getCount() > 0) {
			c.moveToNext();

			Category cate = new Category();
			cate.setIconUrl(c.getString(c
					.getColumnIndexOrThrow(DatabaseSchema.TABLE_CATEGORY.COLUMN_ICON_URL)));
			cate.setName(c.getString(c
					.getColumnIndexOrThrow(DatabaseSchema.TABLE_CATEGORY.COLUMN_NAME)));
			cate.setSig(c.getInt(c
					.getColumnIndexOrThrow(DatabaseSchema.TABLE_CATEGORY.COLUMN_CATEID)));
			cate.setAppCount(c.getInt(c
					.getColumnIndexOrThrow(DatabaseSchema.TABLE_CATEGORY.COLUMN_APPCOUNT)));
			cate.setDescription(c.getString(c
					.getColumnIndexOrThrow(DatabaseSchema.TABLE_CATEGORY.COLUMN_CATE_DESC)));
			cate.setType(c.getInt(c
					.getColumnIndexOrThrow(DatabaseSchema.TABLE_CATEGORY.COLUMN_TYPE)));
			cate.setUpdateInterval(c.getLong(c
					.getColumnIndexOrThrow(DatabaseSchema.TABLE_CATEGORY.COLUMN_UPDATE_INTERVAL)));
			cate.setCateOrder(c.getInt(c
					.getColumnIndexOrThrow(DatabaseSchema.TABLE_CATEGORY.COLUMN_CATE_ORDER)));
			c.close();

			return cate;
		} else {
			MyLog.d(TAG, "no this category" + cateId);
			c.close();

			return null;
		}

	}

	public static ArrayList<Category> getCategoryByType(SQLiteDatabase db,
			int type) {

		StringBuilder buf = new StringBuilder();
		buf.append("SELECT * FROM ");
		buf.append(DatabaseSchema.TABLE_CATEGORY.NAME);
		buf.append(" WHERE ");
		buf.append(DatabaseSchema.TABLE_CATEGORY.COLUMN_TYPE);
		buf.append("=");
		buf.append(type);
		buf.append(" ORDER BY ");
		buf.append(DatabaseSchema.TABLE_CATEGORY.COLUMN_CATE_ORDER);
		MyLog.d(TAG, "run query>>>" + buf.toString());
		Cursor c = db.rawQuery(buf.toString(), null);
		int count = c.getCount();
		MyLog.d(TAG, ">>>>>>>>>>>Cursor count=" + count);
		ArrayList<Category> list = new ArrayList<Category>();
		if (c != null && count > 0) {
			c.moveToFirst();
			for (int i = 0; i < count; i++) {
				Category cate = new Category();
				cate.setIconUrl(c.getString(c
						.getColumnIndexOrThrow(DatabaseSchema.TABLE_CATEGORY.COLUMN_ICON_URL)));
				cate.setName(c.getString(c
						.getColumnIndexOrThrow(DatabaseSchema.TABLE_CATEGORY.COLUMN_NAME)));
				cate.setSig(c.getInt(c
						.getColumnIndexOrThrow(DatabaseSchema.TABLE_CATEGORY.COLUMN_CATEID)));
				cate.setAppCount(c.getInt(c
						.getColumnIndexOrThrow(DatabaseSchema.TABLE_CATEGORY.COLUMN_APPCOUNT)));
				cate.setDescription(c.getString(c
						.getColumnIndexOrThrow(DatabaseSchema.TABLE_CATEGORY.COLUMN_CATE_DESC)));
				cate.setType(c.getInt(c
						.getColumnIndexOrThrow(DatabaseSchema.TABLE_CATEGORY.COLUMN_TYPE)));
				cate.setUpdateInterval(c.getLong(c
						.getColumnIndexOrThrow(DatabaseSchema.TABLE_CATEGORY.COLUMN_UPDATE_INTERVAL)));
				cate.setCateOrder(c.getInt(c
						.getColumnIndexOrThrow(DatabaseSchema.TABLE_CATEGORY.COLUMN_CATE_ORDER)));
				list.add(cate);
				c.moveToNext();
			}

		} else {
			MyLog.d(TAG, "no this category,type" + type);

			return null;
		}
		if (c != null)
			c.close();

		return list;
	}

	public static String getApplistColumnName(int orderType) {
		String applistColumnName = null;
		if (orderType == 1)
			applistColumnName = DatabaseSchema.TABLE_CATEGORY_APP_LIST.COLUMN_APPLIST_1;
		else if (orderType == 2)
			applistColumnName = DatabaseSchema.TABLE_CATEGORY_APP_LIST.COLUMN_APPLIST_2;
		else if (orderType == 3)
			applistColumnName = DatabaseSchema.TABLE_CATEGORY_APP_LIST.COLUMN_APPLIST_3;

		return applistColumnName;
	}

	public static String getUpdateTimeColumnName(int orderType) {
		String applistColumnName = null;
		if (orderType == 1)
			applistColumnName = DatabaseSchema.TABLE_CATEGORY_APP_LIST.COLUMN_APPLIST1_LAST_UPDATE_TIME;
		else if (orderType == 2)
			applistColumnName = DatabaseSchema.TABLE_CATEGORY_APP_LIST.COLUMN_APPLIST2_LAST_UPDATE_TIME;
		else if (orderType == 3)
			applistColumnName = DatabaseSchema.TABLE_CATEGORY_APP_LIST.COLUMN_APPLIST3_LAST_UPDATE_TIME;

		return applistColumnName;
	}

	public static String getAppListInOneCate(SQLiteDatabase db, int cateId,
			int orderType) {
		Log.e("cateid", "==============" + cateId);
		String applistColumnName = getApplistColumnName(orderType);

		StringBuilder buf = new StringBuilder();
		buf.append("SELECT ");
		buf.append(applistColumnName);
		buf.append(" FROM ");
		buf.append(DatabaseSchema.TABLE_CATEGORY_APP_LIST.NAME);
		buf.append(" WHERE ");
		buf.append(DatabaseSchema.TABLE_CATEGORY_APP_LIST.COLUMN_CATEID);
		buf.append("=");
		buf.append(cateId);

		MyLog.d(TAG, buf.toString());

		Cursor c = db.rawQuery(buf.toString(), null);

		String result = null;
		if (c != null && c.getCount() > 0) {
			MyLog.d(TAG, "getAppListInOneCate:" + c.getCount());
			c.moveToNext();
			result = c.getString(0);
		}

		c.close();

		return result;

	}

	public static Cursor getDownloadingCursor(SQLiteDatabase db) {
		StringBuilder buf = new StringBuilder();
		buf.append("SELECT  * FROM ");
		buf.append(DatabaseSchema.TABLE_APP.NAME);
		buf.append(" WHERE ");
		buf.append(DatabaseSchema.TABLE_APP.COLUMN_STATUS);
		buf.append("=");
		buf.append(App.DOWNLOADING);
		buf.append(" OR ");
		buf.append(DatabaseSchema.TABLE_APP.COLUMN_STATUS);
		buf.append("=");
		buf.append(App.PAUSED);
		buf.append(" ORDER BY ");
		buf.append(DatabaseSchema.TABLE_APP.COLUMN_STATUS);
		buf.append(" DESC");

		MyLog.d(TAG, "get downloading cursor >> " + buf);

		return db.rawQuery(buf.toString(), null);
	}

	public static Cursor getDownloadedCursor(SQLiteDatabase db) {

		StringBuilder buf = new StringBuilder();
		buf.append("SELECT  * FROM ");
		buf.append(DatabaseSchema.TABLE_APP.NAME);
		buf.append(" WHERE ");
		buf.append(DatabaseSchema.TABLE_APP.COLUMN_STATUS);
		buf.append("=");
		buf.append(App.DOWNLOADED);
		buf.append(" OR ");
		buf.append(DatabaseSchema.TABLE_APP.COLUMN_STATUS);
		buf.append("=");
		buf.append(App.INSTALLED);
		buf.append(" OR ");
		buf.append(DatabaseSchema.TABLE_APP.COLUMN_STATUS);
		buf.append("=");
		buf.append(App.HAS_UPDATE);
		buf.append(" ORDER BY ");

		buf.append(DatabaseSchema.TABLE_APP.COLUMN_STATUS);

		MyLog.d(TAG, "get downloaded cursor >> " + buf);

		return db.rawQuery(buf.toString(), null);
	}

	public static void clearInstalledToInit(SQLiteDatabase db) {
		StringBuilder buf = new StringBuilder();
		buf.append("UPDATE ");
		buf.append(DatabaseSchema.TABLE_APP.NAME);
		buf.append(" SET ");
		buf.append(DatabaseSchema.TABLE_APP.COLUMN_STATUS);
		buf.append("=");
		buf.append(App.INIT);
		buf.append(" WHERE ");
		buf.append(DatabaseSchema.TABLE_APP.COLUMN_STATUS);
		buf.append("=");
		buf.append(App.INSTALLED);
		buf.append(" OR ");
		buf.append(DatabaseSchema.TABLE_APP.COLUMN_STATUS);
		buf.append("=");
		buf.append(App.HAS_UPDATE);

		db.rawQuery(buf.toString(), null);
	}

	public static HashMap<String, Object> getMyDownloadCursor(SQLiteDatabase db) {
		StringBuilder buf = new StringBuilder();
		buf.append("SELECT  * FROM ");
		buf.append(DatabaseSchema.TABLE_APP.NAME);
		buf.append(" WHERE ");
		buf.append(DatabaseSchema.TABLE_APP.COLUMN_STATUS);
		buf.append("=");
		buf.append(App.DOWNLOADING);
		buf.append(" OR ");
		buf.append(DatabaseSchema.TABLE_APP.COLUMN_STATUS);
		buf.append("=");
		buf.append(App.PAUSED);
		buf.append(" OR ");
		buf.append(DatabaseSchema.TABLE_APP.COLUMN_STATUS);
		buf.append("=");
		buf.append(App.DOWNLOADED);
		buf.append(" OR ");
		buf.append(DatabaseSchema.TABLE_APP.COLUMN_STATUS);
		buf.append("=");
		buf.append(App.INSTALLED);
		buf.append(" OR ");
		buf.append(DatabaseSchema.TABLE_APP.COLUMN_STATUS);
		buf.append("=");
		buf.append(App.HAS_UPDATE);
		buf.append(" ORDER BY ");

		buf.append(DatabaseSchema.TABLE_APP.COLUMN_STATUS);

		MyLog.d(TAG, "get downloaded cursor >> " + buf);

		Cursor c = db.rawQuery(buf.toString(), null);

		HashMap<String, Object> resp = new HashMap<String, Object>();

		ArrayList<App> appDownloadingList = new ArrayList<App>();
		ArrayList<App> appPreInstallList = new ArrayList<App>();
		ArrayList<App> appHasUpdateList = new ArrayList<App>();
		ArrayList<App> appInstalledList = new ArrayList<App>();

		if (c != null && c.getCount() > 0) {
			while (c.moveToNext()) {
				App app = createAppFromCursor(c);

				switch (app.getStatus()) {
				case App.DOWNLOADING:
				case App.PAUSED:
					appDownloadingList.add(app);
					break;

				case App.HAS_UPDATE:
					appHasUpdateList.add(app);
					break;

				case App.DOWNLOADED:
					appPreInstallList.add(app);
					break;

				case App.INSTALLED:
					appInstalledList.add(app);
					break;
				}
				;
			}
		} else {
			c.close();
			return null;
		}

		c.close();

		resp.put("appDownloadingList", appDownloadingList);
		resp.put("appPreInstallList", appPreInstallList);
		resp.put("appHasUpdateList", appHasUpdateList);
		resp.put("appInstalledList", appInstalledList);

		return resp;
	}

	public static int getNeedUpdateAppCount(Context ctx) {

		SQLiteDatabase db = new DatabaseHelper(ctx).getWritableDatabase();
		StringBuilder buf = new StringBuilder();
		buf.append("SELECT  * FROM ");
		buf.append(DatabaseSchema.TABLE_APP.NAME);
		buf.append(" WHERE ");
		buf.append(DatabaseSchema.TABLE_APP.COLUMN_STATUS);
		buf.append("=");
		buf.append(App.HAS_UPDATE);

		Cursor c = db.rawQuery(buf.toString(), null);

		int count = 0;
		if (c != null && c.getCount() > 0) {
			count = c.getCount();
		}

		c.close();
		db.close();

		return count;
	}
	

	public static int getAppStatus(Context ctx, String id) {
		SQLiteDatabase db = new DatabaseHelper(ctx).getWritableDatabase();

		StringBuilder buf = new StringBuilder();
		buf.append("SELECT ");
		buf.append(DatabaseSchema.TABLE_APP.COLUMN_STATUS);
		buf.append(" FROM ");
		buf.append(DatabaseSchema.TABLE_APP.NAME);
		buf.append(" WHERE ");
		buf.append(DatabaseSchema.TABLE_APP.COLUMN_APPID);
		buf.append("=");
		buf.append(id);
		Log.e("===============1231233", "==================");

		Cursor c = db.rawQuery(buf.toString(), null);
		int status = App.INIT;
		if (c != null) {
			if (c.moveToFirst()) {
				status = c.getInt(0);
			}

			c.close();
		}

		db.close();

		return status;
	}

	public static HashMap<String, Integer> getAppInfo(Context ctx, int id) {
		SQLiteDatabase db = new DatabaseHelper(ctx).getWritableDatabase();

		HashMap<String, Integer> result = new HashMap<String, Integer>();

		StringBuilder buf = new StringBuilder();
		buf.append("SELECT ");
		buf.append(DatabaseSchema.TABLE_APP.COLUMN_STATUS);
		buf.append(",");
		buf.append(DatabaseSchema.TABLE_APP.COLUMN_SCORE);
		buf.append(",");
		buf.append(DatabaseSchema.TABLE_APP.COLUMN_SCORE_CNT);
		buf.append(",");
		buf.append(DatabaseSchema.TABLE_APP.COLUMN_COMMENT_CNT);
		buf.append(" FROM ");
		buf.append(DatabaseSchema.TABLE_APP.NAME);
		buf.append(" WHERE ");
		buf.append(DatabaseSchema.TABLE_APP.COLUMN_APPID);
		Log.e("===============1231232", "=====================");
		buf.append("=");
		buf.append(id);

		Cursor c = db.rawQuery(buf.toString(), null);
		int status = App.INIT;
		if (c != null) {
			if (c.moveToFirst()) {
				result.put("status", c.getInt(0));
				result.put("score", c.getInt(1));
				result.put("score_cnt", c.getInt(2));
				result.put("comment_cnt", c.getInt(3));
			}

			c.close();
		}

		db.close();

		return result;
	}

	public static App createAppFromCursor(Cursor c) {
		App app = new App();
		app.setAppVersion(c.getString(c
				.getColumnIndexOrThrow(DatabaseSchema.TABLE_APP.COLUMN_VERSION)));
		app.setAuthorName(c.getString(c
				.getColumnIndexOrThrow(DatabaseSchema.TABLE_APP.COLUMN_AUTHOR)));
		app.setDbPid(c.getInt(c
				.getColumnIndexOrThrow(DatabaseSchema.TABLE_APP.COLUMN_ID)));
		app.setDescription(c.getString(c
				.getColumnIndexOrThrow(DatabaseSchema.TABLE_APP.COLUMN_DESCRIP)));
		app.setDownloadCount(c.getInt(c
				.getColumnIndexOrThrow(DatabaseSchema.TABLE_APP.COLUMN_DOWNLOAD_CNT)));
		app.setIconUrl(c.getString(c
				.getColumnIndexOrThrow(DatabaseSchema.TABLE_APP.COLUMN_ICON_URL)));
		app.setId(c.getInt(c
				.getColumnIndexOrThrow(DatabaseSchema.TABLE_APP.COLUMN_APPID)));
		app.setInfoVersion(c.getInt(c
				.getColumnIndexOrThrow(DatabaseSchema.TABLE_APP.COLUMN_INT_VERSION)));
		app.setLocalPath(c.getString(c
				.getColumnIndexOrThrow(DatabaseSchema.TABLE_APP.COLUMN_LOCALPATH)));
		app.setName(c.getString(c
				.getColumnIndexOrThrow(DatabaseSchema.TABLE_APP.COLUMN_NAME)));
		app.setPrice(c.getString(c
				.getColumnIndexOrThrow(DatabaseSchema.TABLE_APP.COLUMN_PRICE)));
		app.setPackageName(c.getString(c
				.getColumnIndexOrThrow(DatabaseSchema.TABLE_APP.COLUMN_PACKAGENAME)));
		app.setScore(c.getInt(c
				.getColumnIndexOrThrow(DatabaseSchema.TABLE_APP.COLUMN_SCORE)));
		app.setScoreCount(c.getInt(c
				.getColumnIndexOrThrow(DatabaseSchema.TABLE_APP.COLUMN_SCORE_CNT)));
		app.setCommentCount(c.getInt(c
				.getColumnIndexOrThrow(DatabaseSchema.TABLE_APP.COLUMN_COMMENT_CNT)));
		app.setLanguage(c.getInt(c
				.getColumnIndexOrThrow(DatabaseSchema.TABLE_APP.COLUMN_LANGUAGE)));
		app.setScreenshotUrl(c.getString(c
				.getColumnIndexOrThrow(DatabaseSchema.TABLE_APP.COLUMN_SCREENSHOT_URL)));
		// app.setSlogan(c.getString(c.getColumnIndexOrThrow(DatabaseSchema.TABLE_APP.COLUMN_SLOGAN)));
		app.setStatus(c.getInt(c
				.getColumnIndexOrThrow(DatabaseSchema.TABLE_APP.COLUMN_STATUS)));
		app.setDownloadedSize(c.getInt(c
				.getColumnIndexOrThrow(DatabaseSchema.TABLE_APP.COLUMN_DOWNLOADED_SIZE)));
		MyLog.d(TAG, "app status: " + app.getStatus());
		app.setSize(c.getInt(c
				.getColumnIndexOrThrow(DatabaseSchema.TABLE_APP.COLUMN_SIZE)));
		app.setVersion(c.getInt(c
				.getColumnIndexOrThrow(DatabaseSchema.TABLE_APP.COLUMN_VERSION_NUM)));
		app.setLastUpdateTime(c.getInt(c
				.getColumnIndexOrThrow(DatabaseSchema.TABLE_APP.COLUMN_LAST_UPDATE_TIME)));

		return app;
	}

	public static Cursor getSearchResultCursor(SQLiteDatabase db,
			CharSequence text) {
		StringBuilder buf = new StringBuilder();
		buf.append("SELECT * FROM " + DatabaseSchema.TABLE_APP.NAME);
		buf.append(" WHERE " + DatabaseSchema.TABLE_APP.COLUMN_NAME);
		buf.append(" LIKE '%" + text + "%'");
		buf.append(" OR " + DatabaseSchema.TABLE_APP.COLUMN_SLOGAN);
		buf.append(" LIKE '%" + text + "%'");
		buf.append(" OR " + DatabaseSchema.TABLE_APP.COLUMN_DESCRIP);
		buf.append(" LIKE '%" + text + "%'");
		buf.append(" OR " + DatabaseSchema.TABLE_APP.COLUMN_AUTHOR);
		buf.append(" LIKE '%" + text + "%'");

		return db.rawQuery(buf.toString(), null);
	}

	public static String getInstallingAppLocalPath(Context ctx, int appId) {
		// if (TextUtils.isEmpty(appId)) {
		// return null;
		// }

		String localpath = GeneralUtil.LOCAL_DOWNLOAD_APP_PATH + "/" + appId
				+ "_temp.apk";

		return localpath;

	}

	public static void updateInstalledCompleteStatus(SQLiteDatabase db, int pid) {
		StringBuilder buf = new StringBuilder(); 
		buf.append(DatabaseSchema.TABLE_APP.COLUMN_APPID);
		buf.append("=" + pid);
		Log.e("==================321", "=============");
		ContentValues cv = new ContentValues();
		cv.put(DatabaseSchema.TABLE_APP.COLUMN_STATUS, App.INSTALLED);

		MyLog.d(TAG, "updateInstalledCompleteStatus:" + pid);

		try {
			db.update(DatabaseSchema.TABLE_APP.NAME, cv, buf.toString(), null);
		} catch (Exception e) {
			Log.e(TAG, e.toString());
		}
	}

	public static void updateDeleteCompleteStatus(SQLiteDatabase db, int id) {
		StringBuilder buf = new StringBuilder();
		buf.append(DatabaseSchema.TABLE_APP.COLUMN_APPID);
		buf.append("=" + id);
		Log.e("12312313===================", "=========================");

		ContentValues cv = new ContentValues();
		cv.put(DatabaseSchema.TABLE_APP.COLUMN_STATUS, App.INIT);

		try {
			db.update(DatabaseSchema.TABLE_APP.NAME, cv, buf.toString(), null);
		} catch (Exception e) {
			Log.e(TAG, e.toString());
		}
	}

	public static int getCatetoryAppCount(SQLiteDatabase db, int cate_id) {
		StringBuilder buf = new StringBuilder();
		buf.append("SELECT " + DatabaseSchema.TABLE_CATEGORY.COLUMN_APPCOUNT);
		buf.append(" FROM " + DatabaseSchema.TABLE_CATEGORY.NAME);
		buf.append(" WHERE " + DatabaseSchema.TABLE_CATEGORY.COLUMN_CATEID);
		buf.append("='" + cate_id + "'");

		int count = -1;

		Cursor c = db.rawQuery(buf.toString(), null);
		if (c != null && c.moveToFirst()) {
			count = c.getInt(0);
		}

		c.close();
		return count;
	}

	public static long getAppListLastUpdateTime(SQLiteDatabase db, int cate_id,
			int orderType) {

		String updateColumnName = getUpdateTimeColumnName(orderType);

		StringBuilder buf = new StringBuilder();
		buf.append("SELECT " + updateColumnName);
		buf.append(" FROM " + DatabaseSchema.TABLE_CATEGORY_APP_LIST.NAME);
		buf.append(" WHERE "
				+ DatabaseSchema.TABLE_CATEGORY_APP_LIST.COLUMN_CATEID);
		buf.append("='" + cate_id + "'");

		long lastUpdateTime = 0;

		Cursor c = db.rawQuery(buf.toString(), null);
		if (c != null && c.moveToFirst()) {
			lastUpdateTime = c.getLong(0);
		}

		c.close();
		return lastUpdateTime;
	}

	public static void saveApplistInOneCate(SQLiteDatabase db, int cateId,
			int orderType, String applist, long lastUpdateTime) {

		String applistColumnName = getApplistColumnName(orderType);
		String updateColumnName = getUpdateTimeColumnName(orderType);

		ContentValues cv = new ContentValues();
		cv.put(DatabaseSchema.TABLE_CATEGORY_APP_LIST.COLUMN_CATEID, cateId);
		cv.put(applistColumnName, applist);
		cv.put(updateColumnName, lastUpdateTime);

		StringBuilder buf = new StringBuilder();
		buf.append("SELECT ");
		buf.append(DatabaseSchema.TABLE_CATEGORY_APP_LIST.COLUMN_CATEID);
		buf.append(" FROM ");
		buf.append(DatabaseSchema.TABLE_CATEGORY_APP_LIST.NAME);
		buf.append(" WHERE ");
		buf.append(DatabaseSchema.TABLE_CATEGORY_APP_LIST.COLUMN_CATEID);
		buf.append("=");
		buf.append(cateId);

		Cursor c = db.rawQuery(buf.toString(), null);

		boolean isInsert = true;
		if (c != null && c.getCount() > 0) {
			isInsert = false;
		}

		c.close();

		try {
			if (isInsert) {
				db.insertOrThrow(DatabaseSchema.TABLE_CATEGORY_APP_LIST.NAME,
						null, cv);
			} else {
				db.update(DatabaseSchema.TABLE_CATEGORY_APP_LIST.NAME, cv,
						DatabaseSchema.TABLE_CATEGORY_APP_LIST.COLUMN_CATEID
								+ "=?", new String[] { cateId + "" });
			}

		} catch (SQLException e) {
			Log.e(TAG,
					"saveApplistInOneCate replaceOrThrow error=" + e.toString());
		}
	}

	public static ArrayList<App> getBackupCursor(SQLiteDatabase db,
			String appList) {
		StringBuilder buf = new StringBuilder();
		buf.append("SELECT  * FROM ");
		buf.append(DatabaseSchema.TABLE_APP.NAME);
		buf.append(" WHERE ");
		buf.append(DatabaseSchema.TABLE_APP.COLUMN_STATUS);
		buf.append("=");
		buf.append(App.INSTALLED);
		buf.append(" OR ");
		buf.append(DatabaseSchema.TABLE_APP.COLUMN_STATUS);
		buf.append("=");
		buf.append(App.HAS_UPDATE);
		buf.append(" ORDER BY ");

		buf.append(DatabaseSchema.TABLE_APP.COLUMN_STATUS);

		MyLog.d(TAG, "get backup cursor >> " + buf);

		Cursor c = db.rawQuery(buf.toString(), null);

		MyLog.d(TAG, "get cursor count=" + c.getCount());

		ArrayList<App> resp = new ArrayList<App>();

		// add appPreBackupList
		if (c != null && c.getCount() > 0) {
			while (c.moveToNext()) {
				App app = createAppFromCursor(c);

				switch (app.getStatus()) {
				case App.HAS_UPDATE:

					if (appList.indexOf(String.valueOf(app.getId())) == -1) {

						resp.add(app);
					}

					break;

				case App.INSTALLED:

					if (appList.indexOf(String.valueOf(app.getId())) == -1) {

						resp.add(app);
					}
					break;
				}
				;
			}
		}
		if (!c.isClosed()) {
			c.close();
		}

		return resp;
	}

	public static ArrayList<App> getRecoveryCursor(Context ctx, String appList) {

		ArrayList<App> resp = new ArrayList<App>();
		String[] appidList = appList.split(",");
		int length = appidList.length;
		for (int i = 0; i < length; i++) {
			App app = DatabaseUtils.getAppById(ctx,
					Integer.parseInt(appidList[i]));
			if (app == null) {
				return null;
			}
			if (app.getStatus() == App.INIT) {
				resp.add(app);
			}
		}

		return resp;
	}
	// public static void saveCateUpdateTime(SQLiteDatabase db, int cateId, long
	// updateTime) {
	//
	// ContentValues cv = new ContentValues();
	// cv.put(DatabaseSchema.TABLE_CATEGORY.COLUMN_LAST_UPDATE_TIME,
	// updateTime);
	//
	// MyLog.d(TAG, "saveCateUpdateTime " + cateId );
	//
	// try
	// {
	// db.update(DatabaseSchema.TABLE_CATEGORY.NAME,
	// cv,
	// DatabaseSchema.TABLE_CATEGORY.COLUMN_CATEID + "=?",
	// new String[] {cateId+""});
	// }
	// catch(SQLException e)
	// {
	// Log.e(TAG, "saveCateUpdateTime update error="+e.toString());
	// }
	// }
}
