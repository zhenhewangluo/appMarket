
package com.market.hjapp.ui.adapter;

import android.content.Context;
import android.content.res.Resources;
import android.database.Cursor;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.CursorAdapter;
import android.widget.TextView;

import com.market.hjapp.App;
import com.market.hjapp.ImageLoader;
import com.market.hjapp.MyLog;
import com.market.hjapp.R;
import com.market.hjapp.database.DatabaseSchema;
import com.market.hjapp.database.DatabaseUtils;
import com.market.hjapp.ui.view.AppItemView;

public class DownloadListAdapter extends CursorAdapter {
	private static final String TAG = "DownloadListAdapter";
	
    private LayoutInflater mInflater;
    
    private boolean mShowDownloading;

    private int mNeedUpdatesCount;
    
    private Context mContext;

    public DownloadListAdapter(Context context, Cursor c) {
        super(context, c, true);
        
        mInflater = LayoutInflater.from(context);
        mShowDownloading = true;
        mNeedUpdatesCount = 0;
        mContext = context;
    }
    
    public void setShowDownloading(boolean show) {
        mShowDownloading = show;
    }
    
    public boolean getShowDownloading() {
        return mShowDownloading;
    }
    
    public App getAppItem(int pos) {
        Cursor c = (Cursor)getItem(pos);
        
        return DatabaseUtils.createAppFromCursor(c);
    }

    @Override
    public int getCount() {
        int dataCount = super.getCount();
        
        if (!mShowDownloading) {
            // showing downloaded list, need to check whether has apps need to update
            mNeedUpdatesCount = getNeedUpdatesCount(getCursor());
            
            MyLog.d(TAG, "mNeedUpdatesCount " + mNeedUpdatesCount + " dataCount" + dataCount);
            
            if (mNeedUpdatesCount > 0) {
                // show group headers
                return dataCount + 2; 
            } else {
                // if no updates, need to show a text view
                return dataCount + 3;
            }
        }
        
        return dataCount;
    }

    private int getNeedUpdatesCount(Cursor cursor) {
        if (!cursor.moveToFirst()) {
            throw new RuntimeException("Can't move cursor to first >> " + cursor);
        }
        
        int cnt = 0;
        do {
            if (App.HAS_UPDATE == cursor.getInt(
                    cursor.getColumnIndexOrThrow(DatabaseSchema.TABLE_APP.COLUMN_STATUS))) {
                cnt++;
            }
        } while (cursor.moveToNext());

        return cnt;
    }

    @Override
    public void bindView(View view, Context context, Cursor cursor) {
        AppItemView itemView = (AppItemView)view;
        
        itemView.updateView(cursor);
    }

    @Override
    public View newView(Context context, Cursor cursor, ViewGroup parent) {
        return mInflater.inflate(R.layout.app_item, null);
    }

    @Override
    public View getView(int position, View convertView, ViewGroup parent) {
        if (mShowDownloading) {
            // show downloading list, no group headers
            return super.getView(position, convertView, parent);
        }
        
        Resources res = mContext.getResources(); 
        if (mNeedUpdatesCount == 0) {
            // no updates, show header and empty text view
            switch (position) {
                case 0:
                    // update header
                    return getGroupHeader(res.getString(R.string.groupheader_update, 0));
                case 1:
                    // empty text view
                    return getEmptyTextView();
                case 2:
                    // downloaded header
                    return getGroupHeader(res.getString(R.string.groupheader_downloaded, 0));
                default:
                    return getItemView(getCursor(), position - 3, convertView);
            }
        }
        
        if (position == 0) {
            // update header
            return getGroupHeader(res.getString(R.string.groupheader_update, mNeedUpdatesCount));
        }
        
        if (position <= mNeedUpdatesCount) {
            // return update-item view
            return getItemView(getCursor(), position - 1, convertView);
        }
        
        if (position == mNeedUpdatesCount + 1) {
            // downloaded header
            return getGroupHeader(res.getString(R.string.groupheader_downloaded, 
                    getCursor().getCount() - mNeedUpdatesCount));
        }
        
        // item view
        return getItemView(getCursor(), position - mNeedUpdatesCount - 2, convertView);
    }
    
    private View getItemView(Cursor c, int pos, View convertView) {
        if (!c.moveToPosition(pos)) {
            throw new RuntimeException("can't move to position: " + pos);
        }
        
        if (convertView instanceof AppItemView) {
            bindView(convertView, mContext, c);
            return convertView;
        } else {
            return newView(mContext, c, null);
        }
    }

    private View getEmptyTextView() {
        TextView tv = (TextView)mInflater.inflate(R.layout.empty_text, null);
        tv.setText(R.string.no_update_item);
        
        return tv;
    }

    private View getGroupHeader(String string) {
        TextView tv = (TextView)mInflater.inflate(R.layout.group_header, null);
        tv.setText(string);
        
        return tv;
    }
}
