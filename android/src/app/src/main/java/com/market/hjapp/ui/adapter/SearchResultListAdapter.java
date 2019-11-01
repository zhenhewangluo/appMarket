
package com.market.hjapp.ui.adapter;

import android.content.Context;
import android.database.Cursor;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.CursorAdapter;

import com.market.hjapp.App;
import com.market.hjapp.ImageLoader;
import com.market.hjapp.R;
import com.market.hjapp.database.DatabaseUtils;
import com.market.hjapp.ui.view.AppItemView;

public class SearchResultListAdapter extends CursorAdapter {
    private LayoutInflater mInflater;
    
    public SearchResultListAdapter(Context context, Cursor c) {
        super(context, c, true);
        
        mInflater = LayoutInflater.from(context);
    }
    
    public App getAppItem(int pos) {
        Cursor c = (Cursor)getItem(pos);
        
        return DatabaseUtils.createAppFromCursor(c);
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
}
