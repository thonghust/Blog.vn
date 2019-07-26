<div class="table-responsive">
    <table class="table" id="permissions-table">
        <thead>
            <tr>
                <th>Name</th>
        <th>Display Name</th>
        <th>Description</th>
                <th colspan="3">Action</th>
            </tr>
        </thead>
        <tbody>
        @foreach($permissions as $permission)
            <tr>
                <td>{!! $permission->name !!}</td>
            <td>{!! $permission->display_name !!}</td>
            <td>{!! $permission->description !!}</td>
                <td>
                    {!! Form::open(['route' => ['permissions.destroy', $permission->id], 'method' => 'delete']) !!}
                    <div class='btn-group'>
                        <a href="{!! route('permissions.show', [$permission->id]) !!}" class='btn btn-default'><i class="glyphicon glyphicon-eye-open"></i> View</a>
                        <a href="{!! route('permissions.edit', [$permission->id]) !!}" class='btn btn-default'><i class="glyphicon glyphicon-edit"></i> Change</a>
                        {!! Form::button('<i class="glyphicon glyphicon-trash"></i> Delete', ['type' => 'submit', 'class' => 'btn btn-danger', 'onclick' => "return confirm('Are you sure?')"]) !!}
                    </div>
                    {!! Form::close() !!}
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
