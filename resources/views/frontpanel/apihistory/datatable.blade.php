<script>
    $(function() {
        $('#Yajra-dataTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ route("frontend.get-apihistory") }}',
            columns: [
                { data: 'id', name: 'id' },
                { data: 'loc_id', name: 'loc_id' },
                {
                    data: 'data',
                    name: 'data',
                    render: function(data, type, row) {
                        // Truncate long data for display
                        var displayData = data.length > 30 ? data.substring(0, 30) + '...' : data;
                        return displayData + ' <a href="#" class="view-data" data-full-data="' + encodeURIComponent(JSON.stringify(data)) + '" title="View Full Data"><i class="fas fa-eye"></i></a>';
                    }
                },
                { data: 'status', name: 'status' },
                { data: 'date', name: 'date' },
                { data: 'type', name: 'type' },
                { data: 'notes', name: 'notes' },
                { data: 'extra', name: 'extra' }
            ],
            order: [[0, 'desc']]
        });

        // Add click handler for view data eye icon
        $('#Yajra-dataTable').on('click', '.view-data', function(e) {
            e.preventDefault();
            var fullData = decodeURIComponent($(this).data('full-data'));

            // Check if a modal already exists and remove it
            if ($('#dataModal').length) {
                $('#dataModal').remove();
            }

            // Create modal to display data
            var modalHTML = `
            <div class="modal fade" id="dataModal" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Webhook Data</h5>
                          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <pre>${JSON.stringify(JSON.parse(fullData), null, 2)}</pre>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
            `;

            // Append modal to body and show it
            $('body').append(modalHTML);
            $('#dataModal').modal('show');

            // Properly handle modal close events
            $('#dataModal').on('hidden.bs.modal', function () {
                $(this).remove();
            });
        });
    });


    </script>
